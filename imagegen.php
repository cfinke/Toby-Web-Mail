<?php

// This file handles creating the thread arc graphics.

error_reporting(E_ALL ^ E_NOTICE);

include("globals.php");

create_thread_arc($_REQUEST["id"]);

function create_thread_arc($id){
	// Instantiate the thread object.
	$thread = new email_thread($id);
	
	// Get the values of the current message.
	$selected_key = get_selected_key($thread->thread);
	$selected_circle = get_circle_number($selected_key, $thread->flat_thread);
	
	// Set up the configuration values for the graphic.
	$circle_width = 16;
	$circle_border_width = 3;
	$image_width = round(($thread->num_messages * 1.6 * $circle_width) + (0.5 * $circle_width));
	$image_height = $image_width;
	
	// Set up for checking the biggest top and bottom depths.
	$top_depth = 1;
	$bottom_depth = 1;
	
	// Create a blank image
	$image = imagecreate($image_width, $image_height);
	
	// Set up the colors for the image
	$white = imagecolorallocate($image, 255, 255, 255);
	$black = imagecolorallocate($image, 0, 0, 0);
	$default_color = imagecolorallocate($image, 5, 60, 124);
	$highlight = imagecolorallocate($image, 0, 100, 255);
	
	// Run through the messages in chronological order.
	foreach($thread->flat_thread as $key => $value){
		// Get the necessary information for making an arc.
		$begin_circle = $counter++;
		$generation = get_generation($key, $thread->thread);
		$replies = get_reply_keys($key, $thread->thread);
		
		// For each reply, draw an arc.
		if (is_array($replies)){
			foreach($replies as $reply){
				// Get the circle number of the reply.
				$end_circle = get_circle_number($reply, $thread->flat_thread);
				
				// Get the number of circles that must be passed.
				$depth = $end_circle - $begin_circle;
				
				// Get the circle over which the arc must be centered.
				$center_at_circle = ($depth / 2) + $begin_circle;
				
				// Set the color for the arc.
				if (($selected_circle == $begin_circle) || ($selected_circle == $end_circle)) $color = $highlight;
				else $color = $default_color;
				
				// Set the width and height of the arc.
				$arc_w = round($circle_width * 1.7) * $depth;
				$arc_h = $arc_w;
				
				// Set the horizontal center of the arc.
				$center_x = round($circle_width  * 3 / 4) + ($center_at_circle * ($circle_width + 10));				
				
				// Determine whether to use the top or bottom.
				if (($generation % 2) == 0){
					// Check for a new biggest top depth.
					if ($depth > $top_depth) $top_depth = $depth;
					
					// Set the vertical center of the arc.
					$center_y = round(($image_height - $circle_width) / 2);
					
					// Draw the arc.
					imagearc($image, $center_x, $center_y, $arc_w, $arc_h, 180, 0, $color);
				}
				else{
					// Check for a new biggest bottom depth.
					if ($depth > $bottom_depth) $bottom_depth = $depth;
					
					// Set the vertical center of the arc.
					$center_y = round(($image_height + $circle_width) / 2);
					
					// Draw the arc.
					imagearc($image, $center_x, $center_y, $arc_w, $arc_h, 0, 180, $color);
				}
			}
		}
	}
	
	// Draw a circle for each message in the thread.
	for ($i = 0; $i < $thread->num_messages; $i++){
		// Get the thread_id of the current circle.
		$this_key = get_flat_key($i, $thread->flat_thread);
		
		// Determine the color of the circle.
		$color = (in_array($this_key, $thread->unseen)) ? $black : $default_color;
		
		// If this is the selected message, set up the highlighting.
		if ($this_key == $selected_key){
			$extra_w = 5;
			$extra_i = 5;
			$color = $highlight;
		}
		else{
			$extra_w = 0;
			$extra_i = 0;
		}
		
		// Draw the dark colored circle (the border)
		imagefilledellipse($image, round($circle_width  * 3 / 4) + ($i * ($circle_width + 10)), ($image_height / 2), $circle_width + $extra_w, $circle_width + $extra_w, $color);
		
		// If this message was sent by the user, make it hollow.
		if (in_array($this_key, $thread->sent)) imagefilledellipse($image, round($circle_width  * 3 / 4) + ($i * ($circle_width + 10)), ($image_height / 2), $circle_width - (2 * $circle_border_width) + $extra_i, $circle_width - (2 * $circle_border_width) + $extra_i, $white);
	}
	
	## Crop the picture
	
	// Determine the lowest point at which there is data on the image.
	$image_y = round($image_height / 2) - round($circle_width * 0.5) - ($top_depth  * $circle_width) - 5;
	
	// Determine the highest point at which there is data on the image.
	$image_y_bottom = round($image_height / 2) + round($circle_width * 0.5) + ($bottom_depth * $circle_width) + 5;
	
	// Get the height of the new image.
	$new_image_h = $image_y_bottom - $image_y;
	
	// Create a new image to crop the current image.
	$new_image = imagecreate($image_width, $new_image_h);
	
	// Copy the pertinent section of the old image to the new image.
	imagecopy($new_image, $image, 0, 0, 0, $image_y, $image_width, $new_image_h);
	
	// Destory the old image.
	imagedestroy($image);
	
	// Output the new image.
	header("Content-type: image/png");
	imagepng($new_image);
	
	// Destroy the new image.
	imagedestroy($new_image);
}

function get_flat_key($i, $flat_thread){
	foreach($flat_thread as $key => $value){
		if ($i == $counter++){
			return $key;
		}
	}
}

function get_circle_number($key, $flat_thread){
	foreach($flat_thread as $key1 => $value){
		if ($key == $key1){
			return $counter;
		}
		else{
			$counter++;
		}
	}
}

function get_generation($key, $thread){
	foreach($thread as $node){
		if ($node["thread_id"] == $key){
			return $node["generation"];
		}
		else{
			if (is_array($node["sub_thread"])){
				$generation = get_generation($key, $node["sub_thread"]);
				if ($generation != '') break;
			}
		}
	}
	
	return $generation;
}

function get_reply_keys($key, $thread){
	foreach($thread as $node){
		if ($node["thread_id"] == $key){
			if (is_array($node["sub_thread"])){
				foreach($node["sub_thread"] as $reply){
					$keys[] = $reply["thread_id"];
				}
			}
			
			return $keys;
		}
		else{
			if (is_array($node["sub_thread"])){
				$keys = get_reply_keys($key, $node["sub_thread"]);
				if ($keys != '') break;
			}
		}
	}
	
	return $keys;
}

function get_seen($key, $thread){
	foreach($thread as $node){
		if ($node["thread_id"] == $key){
			return $node["seen"];
		}
		else{
			if (is_array($node["sub_thread"])){
				$seen = get_seen($key, $node["sub_thread"]);
				if ($seen != '') break;
			}
		}
	}
	
	return $seen;
}

function get_selected_key($thread){
	foreach($thread as $node){
		if ($node["selected"]){
			return $node["thread_id"];
		}
		else{
			if (is_array($node["sub_thread"])){
				$key = get_selected_key($node["sub_thread"]);
				if ($key != '') break;
			}
		}
	}
	
	return $key;
}

?>