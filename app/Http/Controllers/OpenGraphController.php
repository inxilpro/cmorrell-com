<?php

namespace App\Http\Controllers;

use GDText\Box;
use GDText\Color;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OpenGraphController extends Controller
{
	public function __invoke(Request $request)
	{
		$response = new StreamedResponse();
		
		$text = trim(str_replace('- Chris Morrell', '', $request->input('text')));
		$url = str_replace(url()->to('/'), 'cmorrell.com', url()->to($request->input('url')));
		
		// Don't bother generating the text if nothing's changed
		$response->setEtag(md5("v1|{$text}|{$url}"), weak: true);
		if ($response->isNotModified($request)) {
			return $response;
		}
		
		$im = imagecreatetruecolor(1200, 627);
		$white = imagecolorallocate($im, 255, 255, 255);
		$black = imagecolorallocate($im, 0, 0, 0);
		
		$width = 1200;
		$height = 627;
		$padding = 50;
		$bar_height = 50;
		
		imagefill($im, 0, 0, $white);
		
		// Main text
		$box = new Box($im);
		$box->setFontFace(resource_path('fonts/HouseSlant.ttf'));
		$box->setFontColor(new Color(0, 0, 0));
		$box->setFontSize($this->computeMaxFontSize($text, $width - ($padding * 2), $height - ($padding * 2) - $bar_height));
		$box->setBox($padding, $padding, $width - ($padding * 2), $height - ($padding * 2) - $bar_height);
		$box->setTextAlign('center', 'center');
		$box->draw($text);
		
		// Draw black bar at bottom
		imagefilledrectangle($im, 0, $height - $bar_height, $width, $height, $black);
		
		// URL
		$box = new Box($im);
		$box->setFontFace(resource_path('fonts/FiraCode-Medium.ttf'));
		$box->setFontColor(new Color(255, 255, 255));
		$box->setFontSize(24);
		$box->setBox(20, $height - $bar_height - 1, $width - 40, $bar_height);
		$box->setTextAlign('right', 'center');
		$box->draw($url);
		
		$response->setCallback(fn() => imagepng($im));
		$response->setPublic();
		$response->setMaxAge(60 * 60 * 24 * 365);
		$response->setSharedMaxAge(60 * 60 * 24 * 30);
		$response->setExpires(now()->addYear());
		$response->headers->set('Content-Type', 'image/png');
		$response->headers->addCacheControlDirective('stale-while-revalidate');
		$response->headers->addCacheControlDirective('stale-if-error');
		
		return $response;
	}
	
	protected function computeMaxFontSize(string $text, int $width = 1100, int $height = 527)
	{
		$font = resource_path('fonts/HouseSlant.ttf');
		$font_size = 200;
		
		do {
			$font_size -= 10;
			$points = 0.75 * $font_size;
			
			$lines = $this->wrapText($text, $font, $points, $width);
			
			$line_height = $font_size * 1.2;
			$total_height = count($lines) * $line_height;
		} while ($total_height > $height && $font_size > 10);
		
		return $font_size;
	}
	
	protected function wrapText(string $text, string $font, float $points, int $max_width): array
	{
		$lines = [];
		$words = explode(' ', $text);
		
		if (empty($words)) {
			return $lines;
		}
		
		$current_line = $words[0];
		
		for ($i = 1; $i < count($words); $i++) {
			$test_line = $current_line.' '.$words[$i];
			[$blx, $bly, $brx, $bry, $trx, $try, $tlx, $tly] = imageftbbox($points, 0, $font, $test_line);
			$width = $brx - $blx;
			
			if ($width > $max_width) {
				$lines[] = $current_line;
				$current_line = $words[$i];
			} else {
				$current_line = $test_line;
			}
		}
		
		if (! empty($current_line)) {
			$lines[] = $current_line;
		}
		
		return $lines;
	}
}
