<?php

namespace App\Http\Controllers;

use App\Http\Model\ImageLoad;
use Illuminate\Http\Request;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;

class BookController extends Controller
{
  
   public function test(Request $request)
	{
			//image from app (android)	
			$binary=base64_decode($request->input('logobase64'));
			$androidImage =  time().'_logo.jpg';
			
			//check if image null
			if($request['logobase64'] == null){
					return response()->json(array(
					'status' => "N",
				));
			}
			
			//path to image
			$file = fopen('image/'.$androidImage, 'wb');
			fwrite($file, $binary);
			fclose($file);
			
			//rewrite - path (image located at blog/public/image/)
			$androidImage = 'image/'.$androidImage;
			//$androidImage = 'image/halal-logo.png';


			/*$androidImage2 = imagecreatefrompng($androidImage);
			$rewriteImage = 'image/'.time().'_logo2.jpg';
			imagepng($androidImage2, $rewriteImage, 70);
			imagedestroy($androidImage2);

			$im = file_get_contents($rewriteImage);
			$base64 = base64_encode($im); 
		
			$binary2=base64_decode($base64);

			$file = fopen($rewriteImage, 'wb');
			fwrite($file, $binary2);
			fclose($file);*/
		
			//get image from db.. and compare with recently uploaded image.
			$allImage = ImageLoad::all();
			
			//get the highest only
			$high = 0;
			$dbPosition = 99;
			
			$implementation = new DifferenceHash;
			$hasher = new ImageHash($implementation);

			for($x = 0 ; $x < count($allImage) ; $x++ ){
 
				//$accuracy = $this->compareImages('image/'.$allImage[$x]->logo_path,$androidImage,0.19);
				$accuracy = $hasher->compare('image/'.$allImage[$x]->logo_path,$androidImage);

				if($accuracy <= 20){
					$dbPosition = $x;
				}
		
			}
			
			if($dbPosition == 99){
					return response()->json(array(
					'accuracy' => $accuracy,
					'status' => "N",
				));
		
			}
						
			return response()->json(array(
				'status' => "Y",
				'accuracy' => $accuracy,
				'desc_name' => $allImage[$dbPosition]->desc_name,
				'desc_description' => $allImage[$dbPosition]->desc_description,
			));
		
				
	}
	
	function GetImage($path) {
		$mime = mime_content_type($path);
		switch($mime) {
		  case 'image/png':
			$img = "png";
			break;
		  case 'image/gif':
			$img = "gif";
			break;
		  case 'image/jpeg':
			$img = "jpeg";
			break;
		  case 'image/bmp':
			$img = "bmp";
			break;
		  default:
			$img = "na";
		  }
		  return $img;
		}
	
	public function compareImages($imagePathA, $imagePathB, $accuracy){
	  //load base image
	  $bim = imagecreatefrompng($imagePathA);
	  //create comparison points
	  $bimX = imagesx($bim);
	  $bimY = imagesy($bim);
	  $pointsX = $accuracy*5;
	  $pointsY = $accuracy*5;
	  $sizeX = round($bimX/$pointsX);
	  $sizeY = round($bimY/$pointsY);
	  
	  //load image into an object
	  
	  $im = imagecreatefrompng($imagePathB);
	  
	  
	  //loop through each point and compare the color of that point
	  $y = 0;
	  $matchcount = 0;
	  $num = 0;
	  for ($i=0; $i <= $pointsY; $i++) { 
		$x = 0;
		for($n=0; $n <= $pointsX; $n++){
	  
		  $rgba = imagecolorat($bim, $x, $y);
		  $colorsa = imagecolorsforindex($bim, $rgba);
	  
		  $rgbb = imagecolorat($im, $x, $y);
		  $colorsb = imagecolorsforindex($im, $rgbb);
	  
		  if($this->colorComp($colorsa['red'], $colorsb['red']) && $this->colorComp($colorsa['green'], $colorsb['green']) && $this->colorComp($colorsa['blue'], $colorsb['blue'])){
			//point matches
			$matchcount ++;
		  }
		  $x += $sizeX;
		  $num++;
		}
		$y += $sizeY;
	  }
	  //take a rating of the similarity between the points, if over 90 percent they match.
	  $rating = $matchcount*(100/$num);
	  return $rating;
	}
	


	public function colorComp($color, $c){
		//test to see if the point matches - within boundaries
	  if($color >= $c-2 && $color <= $c+2){
		return true;
	  }else{
		return false;
	  }
	}

   

}