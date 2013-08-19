<html>
<head><title>Parse Janes booklist</title></head> 
<body>
<?php

  $author="";
  $title="";
  $alphabet="";
  $space = " ";
  $comma=",";
  $slash="/";
  $achar="";
  $inputfile="booklist.csv";
  $outputfile="booklist.html";
  $head1="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
  $head2="<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
  $head3="<head>\n";
  $head4="<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n";
  $head5="<title>Jane's Booklist</title>\n";
  $head6="</head>\n";
  $head7="<body>\n";
  $TOC1="<br><a href=\"#A\">A </a><a href=\"#B\">B </a><a href=\"#C\">C </a><a href=\"#D\">D </a><a href=\"#E\">E </a><a href=\"#F\">F </a><a href=\"#G\">G </a><a href=\"#H\">H </a><a href=\"#I\">I </a><a href=\"#J\">J </a><a href=\"#K\">K </a><a href=\"#L\">L </a><a href=\"#M\">M </a><a href=\"#N\">N </a><br>";
  $TOC2="<a href=\"#O\">O </a><a href=\"#P\">P </a><a href=\"#Q\">Q </a><a href=\"#R\">R </a><a href=\"#S\">S </a><a href=\"#T\">T </a><a href=\"#U\">U </a><a href=\"#V\">V </a><a href=\"#W\">W </a><a href=\"#X\">X </a><a href=\"#Y\">Y </a><a href=\"#Z\">Z </a><br>";
  $end1="</body>\n";
  $end2="</html>\n";
  $zchar="";
  
  // open the input and output files
  $finh = fopen($inputfile, 'r') or die("Can't open input file");
  $fouth = fopen($outputfile, 'w') or die("Can't open output file");

  // write the header html
  fwrite($fouth, $head1);
  fwrite($fouth, $head2);
  fwrite($fouth, $head3);
  fwrite($fouth, $head4);
  fwrite($fouth, $head5);
  fwrite($fouth, $head6);
  fwrite($fouth, $head7);
//  fwrite($fouth, $TOC1);
//  fwrite($fouth, $TOC2);

// Main loop
  while(!feof($finh)) 
    {
    $achar = discardspaces($zchar);  // discard spaces  ****
	echo("achar= $achar");
    $getstuff = readthelot($achar);
    echo("<H4> getstuff= $getstuff </H4>");
	}  // End of input file
 
	fclose($finh); // Close input file
	
	fwrite($fouth, $end1);
	fwrite($fouth, $end2);
	fclose($fouth); // Close ouput file
?>
</body>
</html>

<?php

// ****************************************************************************
function readthelot($bchar)
  {
  global $alphabet, $comma, $finh, $fouth, $space, $zchar, $TOC1, $TOC2;
  while(!feof($finh)) 
    {
    if ($bchar < "A" or $bchar > "Z") {fseek($finh, -6, SEEK_CUR); $zchar=fread($finh, 6); echo("X$zchar X"); die ("<H4>Expecting A-Z $bchar </H4>");}
	fwrite($fouth, $TOC1);
    fwrite($fouth, $TOC2);
    $anchor = "<a name=$bchar />"; // Anchor code for TOC item
	$alphabet = $bchar;
    $composite = "<p>".$anchor.$alphabet."<br>";
	echo $composite;
    fwrite($fouth, $composite);
    $bchar = discardspaces($zchar);  // discard spaces  ****
    fseek($finh, -1, SEEK_CUR);  //  move pointer back one character
    $bchar = doaletter();  // do all authors and titles for one letter
    }
  }
// ************************************************************************* 
function gettitle($dchar) // Get the title and write it
  {
  global $slash, $comma, $finh, $fouth, $title;
  echo ("<H4>in gettitle dchar1 = $dchar </H4>");
  while ($dchar != $slash and $dchar != "\n") 
    { $dchar = fread($finh, 1); // Read to end of title
    if ($dchar != $slash) $title = $title.$dchar;
    }
  $buffer = $title."/ ";
  fwrite($fouth, $buffer);  // and write title to output 
  echo ("<H4>in gettitle buffer = $buffer </H4>");
  $title = "";
  return $dchar;
  }
//  ******************************************************************************
function getauthor($cchar)  // Get the author, write it, discard trailing comma
  {
  global $author, $comma, $finh, $fouth;
  $author = "";
  $author = $author.$cchar;
  while ($cchar != $comma) 
    { $cchar = fread($finh, 1); // Read to end of author's name
    echo $cchar;
    if ($cchar != $comma) $author = $author.$cchar;
    }
  $buffer = $author.", ";
  fwrite($fouth, $buffer);
  echo ("<H4>buffer = $buffer</H4>");
  $author = "";
  }
//  ****************************************************************************
function discardspaces($echar)
  {
  global $finh;
  $loop = "0";
  while ($loop == "0")
    { $echar = fread($finh, 1);
    echo ("<H4> echar1 = $echar </H4>"); 
    if ($echar != " ") $loop = "1";  // discard spaces
    }
//  fseek($finh, -1, SEEK_CUR);  //  move pointer back one character
  return($echar);
  } 
//  *****************************************************************************
function lineoftitles()
  {
  global $finh, $fouth;
  $zchar="";
  $bchar="";
  $tit = "";
  while ($tit != "\n")  // do a complete line of titles
    { 
    $dummy = discardspaces($zchar);  // discard spaces  ****
    fseek($finh, -1, SEEK_CUR);  //  move pointer back one character
	$tit = gettitle($bchar);
    }  // do a complete line of titles
	fwrite($fouth, "<br>");
  return;
  }
  //  **************************************************************************
function doaletter()  // deals with all authors and titles for one letter
  {
  global $finh, $fouth, $zchar, $comma;
    $notalpha="1";  // do this until the next letter of the alphabet
  while ($notalpha == "1")
    {
    $bchar = fread($finh, 1);
    echo("<H4>latest bchar= $bchar </H4>");
    if ($bchar == $comma) 
      { $bchar = fread($finh, 1);  //  get first char of author
	   $auth = getauthor($bchar);  // pass first char of author
	   $dummy = discardspaces($zchar);  // discard spaces  ****
       fseek($finh, -1, SEEK_CUR);  //  move pointer back one character
       $dummy = lineoftitles();     // do a complete line of titles
//       $bchar = fread($finh, 1);
       echo("<H4>very latest bchar= $bchar </H4>");
	  }
	else {$notalpha = "0"; echo("<H4>**not a comma** bchar= $bchar </H4>");}
	}
  return $bchar;
  }
	?>
	
