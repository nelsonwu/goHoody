<?php
/*
Uploadify v2.1.4
Release Date: November 8, 2010

Copyright (c) 2010 Ronnie Garcia, Travis Nickels

*/



	
//if (!empty($_FILES)) 
//{
//	
//	//Connect to Hoody MySQL database
//	include_once "misc.inc";
//	include "hoody_functions.php";
//	
//	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
//	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");
//	
//	
//	
//	
//	
//
//	$tempFile = $_FILES['Filedata']['tmp_name'];
//	$file = $_FILES['Filedata']['name'];
//	$file = addslashes($file);
//	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
//	$targetPath =  str_replace('//','/',$targetPath);
//	$upload_filename = '';
//	
//	
//
//	
//	$ext = preg_replace('/^.*\.([^.]+)$/D', '$1', $file);
//	if ($ext != "gif" && $ext != "GIF" && $ext != "JPEG" && $ext != "JPG" && $ext != "jpeg" && $ext != "jpg" && $ext != "PNG" && $ext != "png" || !getimagesize($_FILES['Filedata']['tmp_name']))
//		echo 136;
//	
//	else
//	{
//		$now = time();
//		while(file_exists($upload_filename = $targetPath.$now.'-'.$file))
//			$now++;
//		
//		//Insert data into the Listing_Pictures table
//		$query = "INSERT INTO Pictures_Lookup (URL) VALUES('" . $now . "-" . $file . "')";
//		$result = mysql_query($query) or die (minor_error(253, $fbme, $uid, $today, $query, mysql_error()));		
//		
//		move_uploaded_file($tempFile,$upload_filename);
//		
//		
//		$imgsize = getimagesize($upload_filename);
//		switch(strtolower(substr($upload_filename, -3)))
//		{
//			case "jpg":
//				$image = imagecreatefromjpeg($upload_filename);
//				break;
//			case "png":
//				$image = imagecreatefrompng($upload_filename);
//				break;
//			case "gif":
//				$image = imagecreatefromgif($upload_filename);
//				break;
//			default:
//				exit;
//				break;
//		}
//		
//		$width = 1000; //New width of image
//		$height = $imgsize[1]/$imgsize[0]*$width; //This maintains proportions
//		
//		$src_w = $imgsize[0];
//		$src_h = $imgsize[1];
//		
//		$picture = imagecreatetruecolor($width, $height);
//		imagealphablending($picture, false);
//		imagesavealpha($picture, true);
//		$bool = imagecopyresampled($picture, $image, 0, 0, 0, 0, $width, $height, $src_w, $src_h);
//		
//		if($bool)
//		{
//			switch(strtolower(substr($upload_filename, -3)))
//			{
//				case "jpg":
//					header("Content-Type: image/jpeg");
//					$bool2 = imagejpeg($picture,$upload_filename,80);
//					break;
//				case "png":
//					header("Content-Type: image/png");
//					imagepng($picture,$upload_filename);
//					break;
//				case "gif":
//					header("Content-Type: image/gif");
//					imagegif($picture,$upload_filename);
//					break;
//			}
//		}
//		
//		imagedestroy($picture);
//		imagedestroy($image);
//
//
//
//
//		
//		
//		
//		
//		//echo mysql_insert_id();
//		//echo $now . "-" . $file;
//		$result = array('picture_id' => mysql_insert_id() , 'url' => $now . "-" . $file);
//		echo json_encode($result); 
//			
//	}
//}
//
//
//
//








//working backup
if (!empty($_FILES)) 
{
	//Connect to Hoody MySQL database
	include_once "misc.inc";
	include "hoody_functions.php";
	
	$connection = mysql_connect($host,$user,$password) or die ("couldn't connect to server");
	$db = mysql_select_db($database,$connection) or die ("Couldn't select database");
	
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$file = $_FILES['Filedata']['name'];
	$file = addslashes($file);
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
	$targetPath =  str_replace('//','/',$targetPath);
	$upload_filename = '';
	
	$ext = preg_replace('/^.*\.([^.]+)$/D', '$1', $file);
	if ($ext != "gif" && $ext != "GIF" && $ext != "JPEG" && $ext != "JPG" && $ext != "jpeg" && $ext != "jpg" && $ext != "PNG" && $ext != "png" || !getimagesize($_FILES['Filedata']['tmp_name']))
		echo 136;
	
	else
	{
		$now = time();
		while(file_exists($upload_filename = $targetPath.$now.'-'.$file))
			$now++;
		
		//Insert data into the Listing_Pictures table
		$query = "INSERT INTO Pictures_Lookup (URL) VALUES('" . $now . "-" . $file . "')";
		$result = mysql_query($query) or die (minor_error(253, $fbme, $uid, $today, $query, mysql_error()));		
		
		move_uploaded_file($tempFile,$upload_filename);
		//echo mysql_insert_id();
		//echo $now . "-" . $file;
		$result = array('picture_id' => mysql_insert_id() , 'url' => $now . "-" . $file);
		echo json_encode($result); 
	}
}


?>



<?php
/******************************/
/*      Cadi Web &amp; Design     */
/*     Réalisation de CADI    */
/*          <a href="/forums/profile/_OPS_"> @_OPS_</a>@          */ 
/*    www.cadi-software.com   */
/******************************/


//$width = 75;
//$height = 100;
//
////$_FILES['fichier']['name'] = str_replace(" ","_",$_FILES['fichier']['name']);
////$urltxt     = "http://".$_SERVER["SERVER_NAME"]."/news/news_images/";
///*** the image file to thumbnail ***/
//$image = $urltxt.$url; // Remplacer $urltxt.$url par le chemin du dossier
//
//if(!file_exists($image))
//{
//	echo '';
//}
//else
//{
//	/*** image info ***/
//	list($width_orig, $height_orig, $image_type) = getimagesize($image);
//
//	/*** check for a supported image type ***/
//	if($image_type > 4)
//	{
//		echo 'invalid image';
//	}
//	else
//	{
//		/*** thumb image name ***/
//		$thumb = '../bureau/membre_img/thumbs/'.$url.'';
//		$img = $_FILES['fichier']['name'];
//		$ext = pathinfo($img, PATHINFO_EXTENSION);
//
//
//		/*** maintain aspect ratio ***/
//		if (($width_orig  > $height_orig) && ($width_orig > $width)) 
//		{
//			$height = (int) (($width / $width_orig) * $height_orig);
//		} 
//		elseif($height_orig > 100) 
//		{
//			$height = 100;
//		} 
//		else 
//		{
//			$height = $height_orig;
//		}
//		if (($height_orig > $width_orig) && ($height_orig > $height)) 
//		{
//			$width = (int) (($height / $height_orig) * $width_orig);
//		} 
//		elseif($width_orig > 75) 
//		{
//			$width = 75;
//		} 
//		else 
//		{
//			$width = $width_orig;
//		}
//
//		/*** resample the image ***/
//		$image_p = imagecreatetruecolor($width, $height);
//		if(($ext == 'jpeg') || ($ext == 'jpg') || ($ext == 'JPEG') || ($ext == 'JPG')) 
//		{
//			$image = imagecreatefromjpeg($image);
//		} 
//		elseif ($ext == 'png') 
//		{
//			$image = imagecreatefrompng($image);
//			// fond transparent (pour les png avec transparence)
//			imagesavealpha($image_p, true);
//			$trans_color = imagecolorallocatealpha($image_p, 0, 0, 0, 127);
//			imagefill($image_p, 0, 0, $trans_color);
//		} 
//		elseif ($ext == 'gif') 
//		{
//			$image = imagecreatefromgif($image);
//			// fond transparent (pour les gifs avec transparence)
//			$red = rand(0,255); 
//			$green = rand(0,255); 
//			$blue = rand(0,255); 
//			$transparent = imagecolorallocate($image_p, $red, $green, $blue); 
//			imagefill($image_p, 0, 0, $transparent); 
//			imagecolortransparent($image_p, $transparent);
//			imagetruecolortopalette($image_p, false, 256); 
//		}
//		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
//
//		/*** write the file to disc ***/
//		if(!is_writeable(dirname($thumb)))
//		{
//			echo 'Impossible d\'enregistrer l\'image dans le dossier ' . dirname($thumb);
//		}
//		else
//		{
//			if ($ext == 'png') 
//			{
//				imagepng($image_p, $thumb, 9);
//			} 
//			elseif ($ext == 'gif') 
//			{
//				imagegif($image_p, $thumb, 100);
//			} 
//			else 
//			{
//				imagejpeg($image_p, $thumb, 100);
//			}
//		}
//	}
//}
?>