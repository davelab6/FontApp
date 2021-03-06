<?php
$page = 4;
include ('includes/header.php');
?>
    </HEAD>

    <BODY><?php include ('includes/menu.php'); ?>
	
	<div id="content">
		<div id="classification">
		    <h1>About the classification we used</h1>
		    While there are many fonts available, it is necessary to have a proper classification system to group those fonts.
		    <br><br>
		    One of the leading classification systems in the typography world is the <a href="http://en.wikipedia.org/wiki/Vox-ATypI_classification" target="_blank">Vox-ATypI classification</a>. 
		    In this classification a basic distinction is made between:<br><br>
		    <div id="vox">
		1. CLASSICALS<br><br>
		    1.1 Humanist<br>
		    1.2 Geralde<br>
		    1.3 Transitional<br><br>
		    2. MODERNS<br><br>
		    2.1 Didone<br>
		    2.2 Mechanist<br>
		    2.3 Lineal<br>
		    &nbsp;&nbsp;2.3.1 Grotesque<br>
		    &nbsp;&nbsp;2.3.2 Neo-grotesque<br>
		    &nbsp;&nbsp;2.3.3 Geometric<br>
		    &nbsp;&nbsp;2.3.4 Humanist<br><br>
		    3. CALLIGRAPHICS<br><br>
		    3.1 Glyphic<br>
		    3.2 Script<br>
		    3.3 Graphic<br>
		    3.4 Blackletter<br>
		    3.5 Gaelic
		    </div>
		    <br>
		    
		    This is a very interesting and useful system if you want plunge into typography. But because chronology is the base for this system, we made some adjustments ourselves
		    while categorising the fonts in the FontApp system.<br><br>
		    
		    Basically we draw a line at Mechanist (2.2) and put all categories before that into <b>Serif</b>. It would'nt be beneficial to the
		    system to make distinction between Humanists, Geraldes, etc. We had some doubt about giving Didones it's own category. But because this category would be very small, in
		    the end we decided to put those fonts into Serif as well.<br><br>
		    For the Mechanists (2.2) we made a distinct category, because the character of those fonts differ quite a lot from the other Serifs. We use the term <b>Slab serif</b> for it,
		    this is a term broader accepted over Mechanist.<br><br>
		    
		    One other distinction we made - compared to the Vox-ATypI system - is our category <b>Monospace</b>. In Vox-ATypI this is part of the Mechanists (2.2), but because of its unique
		    character we decided to give it its own category, despite of it's low number.<br><br>
		    
		    We put together all the Lineals (2.3) into a category and named it <b>Sans serif</b>.<br><br>
		    And basically our category <b>Script</b> corresponds to Calligraphics (3).<br><br>
		    
		    That leaves us with our categorie <b>Fun</b>, which is sort of a dump place for fonts with a sort of theme associated with them (e.g. fonts in a Werstern style), dancing fonts or
		    fonts always with a certain degree of 'unseriousness' in it.<br><br>
		    
		    Other fonts which were quite beautiful, but only had capitals available. We didn't want to put them into the Fun category, but neither into its belonging category (often Serifs),
		    because that way you wouldn't be able to compare them to other fonts objectively. This is why we created the category <b>All Caps</b>.<br><br>
		    <br><br><br>
		    
		</div> 
	</div>
	
<?php
include ('includes/footer.php');
?>