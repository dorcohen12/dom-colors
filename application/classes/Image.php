<?php
	defined('INSITE') or die('No direct script access allowed');
	class Image {
        protected $allowedExtensions = ['jpeg', 'jpg', 'png'];
		public $targetDir = 'assets/uploads/';
        protected $final_file;
        protected $file_name;
        protected $total_chunks;
        protected $final_checksum;
        protected $file_path;
        protected $pathinfo;
        protected $ip;
        protected $limit_colors = 5;

        private function normalizeColor($color, $tolerance) {
            return round($color / $tolerance) * $tolerance;
        }

        private function mergeFile() {
            $output = fopen($this->final_file, 'wb');
            for ($i = 0; $i < $this->total_chunks; $i++) {
                $chunkFile = $this->targetDir.$this->file_name.'.part'.$i;
                if (!file_exists($chunkFile)) {
                    return ['error' => $chunkFile.'Missing file for chunk index '.$i];
                }
                $input = fopen($chunkFile, 'rb');
                stream_copy_to_stream($input, $output);
                fclose($input);
                unlink($chunkFile);
            }
            fclose($output);
            $serverFinalChecksum = hash_file('sha256', $this->final_file);
            if (!$serverFinalChecksum === $this->final_checksum) {
                unlink($data['final_file']);
                return ['error' => 'Final checksum is not matched.'];
            }
            $fileInfo = explode('.', $this->file_name);
            $extension = end($fileInfo);
            $new_file_name = base64_encode(time()).'.'.$extension;
            rename($this->targetDir.$this->file_name, $this->targetDir.$new_file_name);
            $this->file_name = $new_file_name;
            return ['success' => 'קובץ הועלה בהצלחה!'];
        }

        private function analyzeColorsPNG($tolerance = 32) {
            /*
                Function is from ChatGPT.
                Tried using without GD, for some reason the expected output was wrong and the function worked fine with BMP files only.
            */
            $image = imagecreatefrompng($this->file_path);
            if (!$image) {
                die("Failed to load image.");
            }

            // Resize the image for faster processing
            $width = imagesx($image);
            $height = imagesy($image);
            $resizedWidth = 100; // Reduced resolution
            $resizedHeight = intval($height * $resizedWidth / $width);
            $resizedImage = imagecreatetruecolor($resizedWidth, $resizedHeight);
            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $resizedWidth, $resizedHeight, $width, $height);

            // Count colors with tolerance
            $colorCounts = [];
            for ($x = 0; $x < $resizedWidth; $x++) {
                for ($y = 0; $y < $resizedHeight; $y++) {
                    $rgb = imagecolorat($resizedImage, $x, $y);
                    $colors = imagecolorsforindex($resizedImage, $rgb);

                    // Normalize colors with tolerance
                    $red = $this->normalizeColor($colors['red'], $tolerance);
                    $green = $this->normalizeColor($colors['green'], $tolerance);
                    $blue = $this->normalizeColor($colors['blue'], $tolerance);

                    // Clamp values to 0-255
                    $red = max(0, min(255, $red));
                    $green = max(0, min(255, $green));
                    $blue = max(0, min(255, $blue));

                    // Format the hex color
                    $hexColor = sprintf("#%02x%02x%02x", $red, $green, $blue);

                    if (!isset($colorCounts[$hexColor])) {
                        $colorCounts[$hexColor] = [
                            'count' => 0,
                            'r' => $red,
                            'g' => $green,
                            'b' => $blue,
                        ];
                    }
                    $colorCounts[$hexColor]['count']++;
                }
            }

            // Sort colors by frequency
            usort($colorCounts, function ($a, $b) {
                return $b['count'] - $a['count'];
            });

            // Calculate percentages and get the top N colors
            $totalPixels = array_sum(array_column($colorCounts, 'count'));
            $dominantColors = [];

            foreach (array_slice($colorCounts, 0, $this->limit_colors) as $color => $data) {
                $percentage = ($data['count'] / $totalPixels) * 100;
                $dominantColors[] = [
                    'color' => [
                        'r' => $data['r'],
                        'g' => $data['g'],
                        'b' => $data['b'],
                        'hex' => sprintf("#%02x%02x%02x", $data['r'], $data['g'], $data['b'])
                    ],
                    'percentage' => round($percentage, 2),
                ];
            }

            // Free resources
            imagedestroy($image);
            imagedestroy($resizedImage);

            return $dominantColors;
        }

        private function analyzeColorsJPG($tolerance = 32) {
            /*
                Function is from ChatGPT.
                Tried using without GD, for some reason the expected output was wrong and the function worked fine with BMP files only.
            */
            $image = imagecreatefromjpeg($this->file_path);
            if (!$image) {
                die("Failed to load image.");
            }

            // Resize the image for faster processing
            $width = imagesx($image);
            $height = imagesy($image);
            $resizedWidth = 100; // Reduced resolution
            $resizedHeight = intval($height * $resizedWidth / $width);
            $resizedImage = imagecreatetruecolor($resizedWidth, $resizedHeight);
            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $resizedWidth, $resizedHeight, $width, $height);

            // Count colors with tolerance
            $colorCounts = [];
            for ($x = 0; $x < $resizedWidth; $x++) {
                for ($y = 0; $y < $resizedHeight; $y++) {
                    $rgb = imagecolorat($resizedImage, $x, $y);
                    $colors = imagecolorsforindex($resizedImage, $rgb);

                    // Normalize colors with tolerance
                    $red = $this->normalizeColor($colors['red'], $tolerance);
                    $green = $this->normalizeColor($colors['green'], $tolerance);
                    $blue = $this->normalizeColor($colors['blue'], $tolerance);

                    // Clamp values to 0-255
                    $red = max(0, min(255, $red));
                    $green = max(0, min(255, $green));
                    $blue = max(0, min(255, $blue));

                    // Format the hex color
                    $hexColor = sprintf("#%02x%02x%02x", $red, $green, $blue);

                    if (!isset($colorCounts[$hexColor])) {
                        $colorCounts[$hexColor] = [
                            'count' => 0,
                            'r' => $red,
                            'g' => $green,
                            'b' => $blue,
                        ];
                    }
                    $colorCounts[$hexColor]['count']++;
                }
            }

            // Sort colors by frequency
            usort($colorCounts, function ($a, $b) {
                return $b['count'] - $a['count'];
            });

            // Calculate percentages and get the top N colors
            $totalPixels = array_sum(array_column($colorCounts, 'count'));
            $dominantColors = [];

            foreach (array_slice($colorCounts, 0, $this->limit_colors) as $data) {
                $percentage = ($data['count'] / $totalPixels) * 100;
                $dominantColors[] = [
                    'color' => [
                        'r' => $data['r'],
                        'g' => $data['g'],
                        'b' => $data['b'],
                        'hex' => sprintf("#%02x%02x%02x", $data['r'], $data['g'], $data['b'])
                    ],
                    'percentage' => round($percentage, 2),
                ];
            }

            // Free resources
            imagedestroy($image);
            imagedestroy($resizedImage);

            return $dominantColors;
        }

        private function getDominantColors() {
            $Website = new Website;
            if(!isset($this->file_name) || empty($this->file_name)) {
                return ['error' => 'Invalid file name set as a var.'];
            }
            $this->file_path = $this->targetDir.$this->file_name;
            if(!file_exists($this->file_path)) {
                return ['error' => 'Final file does not exists.'];
            }
            $this->pathinfo = pathinfo($this->file_path);
            $file_extension = $this->pathinfo['extension'];
            switch($file_extension) {
                case 'png':
                    try {
                        $data = $this->analyzeColorsPNG();
                    } catch (Exception $e) {
                        return ['error' => 'Error => '.$e->getMessage()];
                    }
                    break;
                case 'jpeg':
                    try {
                        $data = $this->analyzeColorsJPG();
                    } catch (Exception $e) {
                        return ['error' => 'Error => '.$e->getMessage()];
                    }
                    break;
                case 'jpg':
                    try {
                        $data = $this->analyzeColorsJPG();
                    } catch (Exception $e) {
                        return ['error' => 'Error => '.$e->getMessage()];
                    }
                    break;
                default:
                //can delete file from here, suspicious
                    unlink($this->file_path);
                    return ['error' => 'Invalid file extension.'];
            }
            if(isset($data) && is_array($data)) {
                $web_cached_images = $Website->GetCachedImages();
                if(!$web_cached_images) {
                    // no cached info
                    $cached_info = [];
                    $cached_info[getUserIP()][$this->file_name] = [
                        'file' => $this->file_name,
                        'data' => $data,
                        'time' => time(),
                    ];
                    $Website->SaveWebImages($cached_info);
                } else {
                    if(isset($web_cached_images[getUserIP()])) {
                        $web_cached_images[getUserIP()][$this->file_name] = [
                            'file' => $this->file_name,
                            'data' => $data,
                            'time' => time(),
                        ];
                    } else {
                        $web_cached_images[getUserIP()][$this->file_name] = [
                            'file' => $this->file_name,
                            'data' => $data,
                            'time' => time(),
                        ];
                    }
                    $Website->SaveWebImages($web_cached_images);
                }
                return $data;
            }
            return ['success' => false];
        }

        public function processImage($data = [], $file = []) {
            if(!is_array($data) || !count($data)) {
                return ['error' => 'System error!'];
            }
            $required_fields = ['file_name', 'chunk_index', 'total_chunks', 'checksum', 'final_checksum'];
            if(!CheckFields($required_fields, $data)) {
                return ['error' => 'Please fill all required information!'];
            }
            if(!count($file)) {
                return ['error' => 'Invalid file chunk data.'];
            }
            $chunkData = file_get_contents($file['tmp_name']);
            $serverChecksum = hash('sha256', $chunkData);

            if (!$serverChecksum === $data['checksum']) {
                return ['error' => $serverChecksum.'test123'];
            }

            if(!is_dir($this->targetDir)) {
                mkdir($this->targetDir);
            }
            $this->targetDir .= getUserIP().'/';
            if(!is_dir($this->targetDir)) {
                mkdir($this->targetDir);
            }
            
            $this->file_data = explode('.', $data['file_name']);

            $tempChunkFile = $this->targetDir.$data['file_name'].'.part'.$data['chunk_index'];
            if (!file_put_contents($tempChunkFile, $chunkData)) {
                return['error' => 'Failed to save chunk'];
            }
            
            if ((int)$data['chunk_index'] + 1 == (int)$data['total_chunks']) {
                // latest chunk, merge file.
                $this->final_file = $this->targetDir.$data['file_name'];
                $this->file_name = $data['file_name'];
                $this->total_chunks = $data['total_chunks'];
                $this->final_checksum = $data['final_checksum'];

                $upload_file = $this->mergeFile();
                if(isset($upload_file['error'])) {
                    return ['error' => $upload_file['error']];
                }
                if(isset($upload_file['success'])) {
                    // get dom colors
                    $test = $this->getDominantColors();
                    return ['success' => $upload_file['success']];
                }
            }
            return [];
        }
    }
