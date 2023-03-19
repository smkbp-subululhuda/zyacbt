<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use PhpOffice\PhpWord\PhpWord;

class Word extends Member_Controller {
    function __construct(){
		parent:: __construct();
	}
    
	public function index(){
		$html = '
			<p>Hal om</p>

			<p><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJcAAAAsCAIAAADn8L2YAAAAAXNSR0IArs4c6QAAAAlwSFlzAAAOxAAADsQBlSsOGwAABSVJREFUeF7tWzt26jAQFW8tkCKHFcAKnDRUtHR2CU26V6ZLY5ehS0tFE3sFYQUcith74c3IH2TZlkY6GPyUqIGD5ZE0d366EuzsZIt9xmZhHM4YfqZOrlFY1B9YpnstYYs0ZJtX9nFGPJ1vbqLoeV56YuHHepx9H6cPY9dhdBNFxpL9lqOXnqYLL8syt3F0FEVwQX/hMQaf26fRW+q4N44gR7ptpz9hdY764k+ATljjL4ouAO4Iilk0H9HbPCJUO0kAAoMECiX8MiK9cy+LcATF8fpvvi/0Y/UOP+VEgL4lwR62nLPjPpjvF2f4djil+rfu1sMdWqPY3+twhG5KNienfZDuuXwDFMt38EfdEDfXKbv5iD0OWDiamnITEGlORXx4+S69opTQ4/K6RTuF4hlDH0Y1S+q0Bg+KKpwO/M+P09CvCNmh4egYikUcJCTIFsOux1oOXd6Lx+q6ZQhP9d7HbeuaYVgWKKCIjyyNWL+QShvXXEzHsMQEKb9tBAwia7CU2DfoTNFmXWBRo2KlvoITgK91n1yV934+L/a1oh3L+HmU8I3CtYp5753b6vbJSCAwr7PHCbnKnDzOtnvYh5Ba9s1ANN8Odc/JRBWSQI5iFq12y7RfBMvVgoqXu1W5YevpCGm8/kKHPGyqgbTKBsaVmZx+jJ+Xs+M3Yd+JI8MBCz9YmYbdWjZShSwQ3Nc6V5tFlSpQCHUDTzo8jMOPVw86pB1kMS3dBqQR5uqrUEbBIsRr01WLKqTj0UKELBB8MfvcHUysUGvWug41M+7vCKkKrEjA6JqpKzI2fpgymjPiAUsM1orEgeqQrE0VmISElsfLhsAW7iZns/jSKxZKp4SO5zl5hckgCuq6LKkQ/RFSKUMi2Cg5b7z+4AnylcK4WS6x9lq76iD8QVb0Fn79kExWjl4V5VhNgfkeSwhmcRim/KdQG+PUEfVCfcgFnUEwotRrqj7UwttiSs0YbKa6ckNgmZfEVcNOo20BqsV33GSRo76wSDnxWidiU0zp2NB7VnPoyKQku1Eox3SN2B8iqlG1BU59CdU1K5JKXCzcl8983wKJlxVfeUwwS8T2ETUJJptp/A5H/rRGy3I0WepeCuVYice8CDCyzdulBIDovmJL/7D7zLIoIpQGypFRl1g9JWVmRFDDF7Jupfxemqp2X5QET1ufjCHWKmattR4yVV1DOWZzqHrnWqkCSp3R13A5yoheBl5kIJHcLFLvbaIpKazVYpdxSG0EVBPVdSjHJpriOwIDJ1QjRGmmedl8BOJEpG7mGIqGLCa+bmL9NtZIXL97bDjnrm0IhBZcus10UCCKvkjEfeDdbDHEZcmgdfNJwwLRNRQpPJoiD0jgFIxY8S8BcU+ds4aDaS5FVFo6VJcxIo4FiCWWJWYD80OpuhmMZdlNhFhk8oJZmTZLQXlpfamuy2J+WG7IZ+WKL3YQSu3bLxIOhctRYrSd2V3xLTduMmbR69Zyv9z5WnriJz1AYuf0R3ArSt1mIVe0CKdEXXyQuznJfe+mAGf+bQPcF7KmZzptamPzA33HjYiKZDDcDud/dtO0glynHE/qRA3nuTMowgE44YYSJNAj7vTiqcGVnOGg1TUTd1CULxRJJ1q580EnfkYGNcvwsaHP0BkU4cgOLkTAdcyX4jJm640VumL+q56uoNi8odTqi5NHBtsGfv/ophfG+raJu1XH1x24YsskrqUxCvVW4XWn17M0Z3YafVv7oOW7ElEHreTeJ/eLYu8qvsEA/wD0sF6yeEP9VgAAAABJRU5ErkJggg==" style="height:44px; width:151px" /></p>
			<p><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJcAAAAsCAIAAADn8L2YAAAAAXNSR0IArs4c6QAAAAlwSFlzAAAOxAAADsQBlSsOGwAABSVJREFUeF7tWzt26jAQFW8tkCKHFcAKnDRUtHR2CU26V6ZLY5ehS0tFE3sFYQUcith74c3IH2TZlkY6GPyUqIGD5ZE0d366EuzsZIt9xmZhHM4YfqZOrlFY1B9YpnstYYs0ZJtX9nFGPJ1vbqLoeV56YuHHepx9H6cPY9dhdBNFxpL9lqOXnqYLL8syt3F0FEVwQX/hMQaf26fRW+q4N44gR7ptpz9hdY764k+ATljjL4ouAO4Iilk0H9HbPCJUO0kAAoMECiX8MiK9cy+LcATF8fpvvi/0Y/UOP+VEgL4lwR62nLPjPpjvF2f4djil+rfu1sMdWqPY3+twhG5KNienfZDuuXwDFMt38EfdEDfXKbv5iD0OWDiamnITEGlORXx4+S69opTQ4/K6RTuF4hlDH0Y1S+q0Bg+KKpwO/M+P09CvCNmh4egYikUcJCTIFsOux1oOXd6Lx+q6ZQhP9d7HbeuaYVgWKKCIjyyNWL+QShvXXEzHsMQEKb9tBAwia7CU2DfoTNFmXWBRo2KlvoITgK91n1yV934+L/a1oh3L+HmU8I3CtYp5753b6vbJSCAwr7PHCbnKnDzOtnvYh5Ba9s1ANN8Odc/JRBWSQI5iFq12y7RfBMvVgoqXu1W5YevpCGm8/kKHPGyqgbTKBsaVmZx+jJ+Xs+M3Yd+JI8MBCz9YmYbdWjZShSwQ3Nc6V5tFlSpQCHUDTzo8jMOPVw86pB1kMS3dBqQR5uqrUEbBIsRr01WLKqTj0UKELBB8MfvcHUysUGvWug41M+7vCKkKrEjA6JqpKzI2fpgymjPiAUsM1orEgeqQrE0VmISElsfLhsAW7iZns/jSKxZKp4SO5zl5hckgCuq6LKkQ/RFSKUMi2Cg5b7z+4AnylcK4WS6x9lq76iD8QVb0Fn79kExWjl4V5VhNgfkeSwhmcRim/KdQG+PUEfVCfcgFnUEwotRrqj7UwttiSs0YbKa6ckNgmZfEVcNOo20BqsV33GSRo76wSDnxWidiU0zp2NB7VnPoyKQku1Eox3SN2B8iqlG1BU59CdU1K5JKXCzcl8983wKJlxVfeUwwS8T2ETUJJptp/A5H/rRGy3I0WepeCuVYice8CDCyzdulBIDovmJL/7D7zLIoIpQGypFRl1g9JWVmRFDDF7Jupfxemqp2X5QET1ufjCHWKmattR4yVV1DOWZzqHrnWqkCSp3R13A5yoheBl5kIJHcLFLvbaIpKazVYpdxSG0EVBPVdSjHJpriOwIDJ1QjRGmmedl8BOJEpG7mGIqGLCa+bmL9NtZIXL97bDjnrm0IhBZcus10UCCKvkjEfeDdbDHEZcmgdfNJwwLRNRQpPJoiD0jgFIxY8S8BcU+ds4aDaS5FVFo6VJcxIo4FiCWWJWYD80OpuhmMZdlNhFhk8oJZmTZLQXlpfamuy2J+WG7IZ+WKL3YQSu3bLxIOhctRYrSd2V3xLTduMmbR69Zyv9z5WnriJz1AYuf0R3ArSt1mIVe0CKdEXXyQuznJfe+mAGf+bQPcF7KmZzptamPzA33HjYiKZDDcDud/dtO0glynHE/qRA3nuTMowgE44YYSJNAj7vTiqcGVnOGg1TUTd1CULxRJJ1q580EnfkYGNcvwsaHP0BkU4cgOLkTAdcyX4jJm640VumL+q56uoNi8odTqi5NHBtsGfv/ophfG+raJu1XH1x24YsskrqUxCvVW4XWn17M0Z3YafVv7oOW7ElEHreTeJ/eLYu8qvsEA/wD0sF6yeEP9VgAAAABJRU5ErkJggg==" style="height:44px; width:151px" /></p>
		';
		
		$doc = new DOMDocument();
		$doc->loadHTML($html);
		$tags = $doc->getElementsByTagName('img');
		foreach ($tags as $tag) {
			$image = $tag->getAttribute('src');
			if (strpos($image, 'data:image/') !== false) {
				$data = preg_split("@[:;,]+@", $image);

				$extensi = explode('/', $data[1]);

				$file_name = uniqid().'.'.$extensi[1];
				
				//echo $data[1].'<br />'; // tipe file
				//echo $data[3].'<br />'; // data base64
			}

			$html = str_replace($image, 'baru.img', $html);
		}

		echo base_url();

		echo $html;
	}
	
    public function tes(){
    	$inputFileName = './public/uploads/Sample_11_ReadWord2007.docx';
    	$phpWord = \PhpOffice\PhpWord\IOFactory::load($inputFileName);

    	$sections = $phpWord->getSections();
    	foreach ($sections as $section) {
    		foreach($section->getElements() as $element) {
    			if($element instanceof PhpOffice\PhpWord\Element\Text){
                    echo($element->getText());
                }else if($element instanceof PhpOffice\PhpWord\Element\Image){
                	
                	$data = 'data:'.$element->getImageType().';base64,'.$element->getImageStringData(TRUE);

                	$gambar = $element->getImageStringData(TRUE);
                	$style_gambar = $element->getStyle();

                	echo $style_gambar->getWidth().' - '.$style_gambar->getHeight();

                	file_put_contents('./public/uploads/aaa1.jpg',base64_decode($gambar));

                }else if($element instanceof PhpOffice\PhpWord\Element\TextBreak){
                	echo '<br />';
                }else if($element instanceof PhpOffice\PhpWord\Element\TextRun){
                	foreach ($element->getElements() as $textrun) {
                		if($textrun instanceof PhpOffice\PhpWord\Element\Text){
							echo $textrun->getText();
						}else if($textrun instanceof PhpOffice\PhpWord\Element\TextBreak){
							echo '<br />';
						}else if($textrun instanceof PhpOffice\PhpWord\Element\Image){
						    $gambar = $textrun->getImageStringData(TRUE);
		                	$style_gambar = $textrun->getStyle();

		                	echo $style_gambar->getWidth().' - '.$style_gambar->getHeight();

		                	file_put_contents('./public/uploads/aaa2.jpg',base64_decode($gambar));
						}else{
						    echo 'lainnya';
						}
					}
                }else{
                	echo "lain";
                }
    		}
    		
    	}
    }

    function format(){
    	$inputFileName = './public/form/tes1.odt';
    	$phpWord = \PhpOffice\PhpWord\IOFactory::load($inputFileName, 'ODText');

    	$sections = $phpWord->getSections();
    	foreach ($sections as $section) {
    		foreach($section->getElements() as $element) {
    			if($element instanceof PhpOffice\PhpWord\Element\Text){
    				echo $element->getText();
    			}else if($element instanceof PhpOffice\PhpWord\Element\TextRun){
					foreach ($element->getElements() as $textrun) {
						if($textrun instanceof PhpOffice\PhpWord\Element\Text){
							echo $textrun->getText();
						}else if($textrun instanceof PhpOffice\PhpWord\Element\TextBreak){
							echo '<br />';
						}else if($textrun instanceof PhpOffice\PhpWord\Element\Line){
							echo '<br />';
						}else if($textrun instanceof PhpOffice\PhpWord\Element\Image){
							echo 'Gambar';
						}else if($element instanceof PhpOffice\PhpWord\Element\Object){
							echo 'Object';
						}else{
							echo 'lainnya';
						}
					}
				}else if($element instanceof PhpOffice\PhpWord\Element\TextBreak){
					echo '<br />';
				}else if($element instanceof PhpOffice\PhpWord\Element\Row){
					echo '<br />';
				}else if($element instanceof PhpOffice\PhpWord\Element\Image){
					echo 'Gambar';
				}else if($element instanceof PhpOffice\PhpWord\Element\Shape){
					echo 'Object';
				}else{
					echo 'lainnya';
				}
    		}
    	}
    }

    function tabel(){
    	$inputFileName = './public/form/form-soal-ganda.xml';
    	$phpWord = \PhpOffice\PhpWord\IOFactory::load($inputFileName);

    	$sections = $phpWord->getSections();
    	foreach ($sections as $section) {
    		foreach($section->getElements() as $element) {
    			if($element instanceof PhpOffice\PhpWord\Element\Table){
                    echo 'Table Soal<br />';

                    foreach ($element->getRows() as $row){
        				foreach ($row->getCells() as $cell){
            				foreach ($cell->getElements() as $element_cell) {
            					if($element_cell instanceof PhpOffice\PhpWord\Element\Text){
				                    echo $element_cell->getText();
				                }else if($element_cell instanceof PhpOffice\PhpWord\Element\TextRun){
				                	foreach ($element_cell->getElements() as $textrun) {
				                		if($textrun instanceof PhpOffice\PhpWord\Element\Text){
						                    echo $textrun->getText();
						                }else if($textrun instanceof PhpOffice\PhpWord\Element\TextBreak){
						                    echo '<br />';
						                }else if($textrun instanceof PhpOffice\PhpWord\Element\Image){
						                	echo 'Gambar';
										}else if($element_cell instanceof PhpOffice\PhpWord\Element\Shape){
											echo 'Gambar';
						                }else{
						                	echo 'lainnya';
						                }
				                	}
				                }else if($element_cell instanceof PhpOffice\PhpWord\Element\TextBreak){
				                    echo '<br />';
				                }else if($element_cell instanceof PhpOffice\PhpWord\Element\Image){
				                	echo 'Gambar';
				                }else if($element_cell instanceof PhpOffice\PhpWord\Element\Shape){
				                	echo 'Gambar';
				                }else{
				                	echo 'lainnya';
				                }
            				}
            			}
        				echo '<br>';
    				}
                }
    		}
    	}
    }
}