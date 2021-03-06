<?php
$page = 1;
if (isset($_GET['id'])) { $id = $_GET['id']; } else { $id = 294; }
if (isset($_GET['size'])) { $size = $_GET['size']; } else { $size = 200; }
if (isset($_GET['line'])) { $line = $_GET['line']; } else { $line = 50; }
if (isset($_GET['align'])) { $align = $_GET['align']; } else { $align = "left"; }
if (isset($_GET['color'])) { $color = $_GET['color']; $setcolor = 1; } else { $color = "0,0,0,100"; $setcolor = 0; }
if (isset($_GET['text'])) { $text_1 = $_GET['text']; } else { $text_1 = "The quick brown fox jumps over the lazy dog"; }
$text_2 = "LOREM IPSUM DOLOR SIT";
$text_3 = "Aut viam inveniam aut faciam ";
$text_4 = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce in elit velit. Vivamus sit amet risus dictum, consequat tellus consequat, fringilla augue. Pellentesque placerat vel dui non venenatis. Integer interdum nisl hendrerit felis lobortis consequat. Vivamus a tempor velit. Morbi tellus purus, ullamcorper eget sodales a, tincidunt luctus dui. Curabitur at elit ligula.";
$text_5 = "FontApp";
$text_6 = "Say \"Hi!\" to info@fontapp.org";
include ('includes/header.php');
?>

		<link rel="stylesheet" type="text/css" media="all" href="includes/fontface_v2.css">
		<script type="text/JavaScript" src="js/font_v2.js"></script>
		<script type="text/JavaScript" src="js/drag.js"></script>
		
		<script language='javascript'>
			var found_font = { category: "01. Sans Serif", style: "Regular", id: 0, height: 0, file_name: "", correct_height: 0 };
			
			var this_field = 1;
			var textfield = new Array ();
			textfield[1] = { id: <?php echo $id; ?>, size: <?php echo $size; ?>, line: <?php echo $line; ?>, align: "<?php echo $align; ?>", color: [<?php echo $color; ?>], focus: 0 }
			textfield[2] = { id: 138, size: 0, line: 50, align: "center", color: [0,0,0,100], focus: 0 }
			textfield[3] = { id: 23, size: 100, line: 30, align: "center", color: [0,0,0,100], focus: 0 }
			textfield[4] = { id: 901, size: 0, line: 75, align: "left", color: [0,0,0,100], focus: 0 }
			textfield[5] = { id: 294, size: 80, line: 60, align: "center", color: [0,0,0,100], focus: 0 }
			textfield[6] = { id: 683, size: 15, line: 70, align: "center", color: [0,40,30,0], focus: 0 }
			
			var properties = new Array (1, 1, 1, 1, 1, 1);	
			var top_items = new Array (1, 1, 1, 1, 1);
			var drops = new Array (0, 0, 0);
			var favorites = new Array();
			var colorbox = 0;
			var screen_width = 0;
			
			var this_mode = 0;
			var mode = new Array();
			mode[0] = { x: 0, y: 165, height: 300, width: 0, field: [1] };
			mode[1] = { x: 400, y: 8, height: 0, width: 0, field: [2,3,4] };
			mode[2] = { x: 400, y: 140, height: 240, width: 370, field: [5,6] };
			
			var old_font = new Array("Arial", "400", "Normal");
			
			var overrule = [];
			var timeouts = [];

			////////////////////////////////////////////////////////////////////
			// II. main functions
			
			function init() {
				close_top_item(1);
				close_top_item(2);
				close_top_item(3);
				deactivate(2);
				deactivate(4);
				deactivate(5);
				fill_initial_fields ();
				field_focus (1);
				set_found_font (<?php echo $id; ?>)
			}
			
			function fill_initial_fields () {
				for (i = 1; i < 7; i++) {
					var id = textfield[i].id;
					var a = textfield[i].size;
					if (i == 1) { a *= 2; }
					var height = correct_height (a, font[id][2]);
					var line = (textfield[i].line + 50) * height / 100;
					update_image (id, i)
					document.getElementById("textfield_" + i).style.fontSize = height + "px";
					document.getElementById("textfield_" + i).style.lineHeight =  line + "px";
					document.getElementById("textfield_" + i).style.textAlign =  textfield[i].align;
					var color = cmyk_to_rgb (textfield[i].color[0], textfield[i].color[1], textfield[i].color[2], textfield[i].color[3]);
					document.getElementById("textfield_" + i).style.color =  "#" + color;
				}	
			}
			
			
			function find_best_match (q) {
				close_message ();
				if (q.id == "size_slider") {
					change_size ();
				}
				else if (q.id == "line_height_slider") {
					change_line_height ();
				}
				else if (q.id == "c" || q.id == "m" ||q.id == "y" ||q.id == "k") {
					change_color ();
				}
				else {
					var difference_array = new Array();
					for (var i = 1; i < font.length; i++) {
						if (font[i][3] == found_font.category && found_font.style == font[i][10]) {
							var difference = 0;
							for (j = 1; j < 6; j++) {
								if (properties[j] == 1) {
									var prop_focus = anti_normal (ExtractNumber (document.getElementById("slider_" + j).style.left));
									difference += Math.abs (font[i][j + 3] - prop_focus);
								}
							}
							difference_array[i] = new Array (i, difference);
						}
					}
					
					difference_array.sort ( function (a, b) { return a[1] - b[1] } );
					var q = difference_array[0][0];
					make_text (q);
				}
			}
			
			function anti_normal (x) {
				var y = x + 12 * Math.sin (x / 15.71);
				return y;
			}
			
			function set_found_font (id) {
				found_font.file_name = font[id][1];
				found_font.height = font[id][2];
				found_font.id = id;
				found_font.category = font[id][3];
				found_font.style = font[id][10];
			}
			
			function make_text (id) {
				set_found_font (id);
				update_image (id, this_field);
				set_properties (id);
			}
			
			function update_image (id, field) {
				document.getElementById("url").style.display = "none";
				textfield[field].id = id;
				
				var font_family = font[id][0];
				var font_weight = font[id][9];
				if (font_weight == "Thin" || font_weight == "Hairline") { font_weight = "100"; }
				if (font_weight == "ExtraLight") { font_weight = 200; }
				if (font_weight == "Light") { font_weight = 300; }
				if (font_weight == "Regular") { font_weight = 400; }
				if (font_weight == "Medium") { font_weight = 500; }
				if (font_weight == "SemiBold") { font_weight = 600; }
				if (font_weight == "Bold") { font_weight = 700; }
				if (font_weight == "ExtraBold") { font_weight = 800; }
				if (font_weight == "Black") { font_weight = 900; }
				
				var font_style = font[id][10];
				if (font_style == "Regular") { font_style = "Normal"; }
				
				var new_font = new Array (font_family, font_weight, font_style);
				if (font[id][12] == "google") {
					WebFont.load ({
						google: { families: [new_font[0] + ":" + font_weight + font_style.toLowerCase()] },
						loading: function() {
							timeouts.push (setTimeout (loading_gif, 500) );
							overrule[field] = 0;
							document.getElementById("textfield_" + field).style.fontFamily = old_font[0];
							document.getElementById("textfield_" + field).style.fontWeight = old_font[1];
							document.getElementById("textfield_" + field).style.fontStyle = old_font[2];
						 },
						 active: function() {
							for (var i = 0; i < timeouts.length; i++) {
								clearTimeout(timeouts[i]);
							}
							timeouts = [];
							
							document.getElementById("loading").style.display = "none";
							overrule[field] = 1;
							document.getElementById("textfield_" + field).style.visibility = "visible";
							document.getElementById("test").innerHTML = "";
							document.getElementById("textfield_" + field).style.fontFamily = new_font[0];
							document.getElementById("textfield_" + field).style.fontWeight = new_font[1];
							document.getElementById("textfield_" + field).style.fontStyle = new_font[2];
							change_size ();
							update_margin ();
							old_font = new_font;
						 },
						 inactive: function() {
							if (overrule[field] == 0) {
								for (var i = 0; i < timeouts.length; i++) {
									clearTimeout(timeouts[i]);
								}
								timeouts = [];
								document.getElementById("loading").style.display = "none";
								document.getElementById("textfield_" + field).style.visibility = "hidden";
								document.getElementById("test").innerHTML = "Problems finding " + new_font[0] + " " + new_font[1] + " " + new_font[2] +  ". We are looking at it, if you are using Safari, try Firefox or Chrome instead";
							}
						 }
						 
					});
				}
				else if (font[id][12] == "custom") {
					WebFont.load ({
						custom: { families: [new_font[0] + ":" + font_weight + font_style.toLowerCase()] },
						loading: function() {
							timeouts.push (setTimeout (loading_gif, 500) );
							overrule[field] = 0;
							document.getElementById("textfield_" + field).style.fontFamily = old_font[0];
							document.getElementById("textfield_" + field).style.fontWeight = old_font[1];
							document.getElementById("textfield_" + field).style.fontStyle = old_font[2];
						 },
						 active: function() {
							for (var i = 0; i < timeouts.length; i++) {
								clearTimeout(timeouts[i]);
							}
							timeouts = [];
							
							document.getElementById("loading").style.display = "none";
							overrule[field] = 1;
							document.getElementById("textfield_" + field).style.visibility = "visible";
							document.getElementById("test").innerHTML = "";
							document.getElementById("textfield_" + field).style.fontFamily = new_font[0];
							document.getElementById("textfield_" + field).style.fontWeight = new_font[1];
							document.getElementById("textfield_" + field).style.fontStyle = new_font[2];
							change_size ();
							update_margin ();
							old_font = new_font;
						 },
						 inactive: function() {
							document.getElementById("test").innerHTML = overrule;
							if (overrule[field] == 0) {
								for (var i = 0; i < timeouts.length; i++) {
									clearTimeout(timeouts[i]);
								}
								timeouts = [];
								document.getElementById("loading").style.display = "none";
								document.getElementById("textfield_" + field).style.visibility = "hidden";
								document.getElementById("test").innerHTML = "Problems finding " + new_font[0] + " " + new_font[1] + " " + new_font[2] +  ". We are looking at it, if you are using Safari, try Firefox or Chrome instead";
							}
						 }
						 
					});
				}
			}
			
			function loading_gif () {
				document.getElementById("loading").style.display = "block";
			}
			
			function update_margin () {
				// correct x-line for height correction
				var a = 0.5 * ExtractNumber (document.getElementById("size_slider").style.left) + 20;
				var b = a - found_font.correct_height;
				if (this_field == 1 ) { b += 70; }
				document.getElementById("textfield_" + this_field).style.marginTop = b + "px";
			}
			
			function count_fonts () {
				var count_fonts = 0;
				for (var i = 1; i < font.length; i++) {
					if (font[i][3] == found_font.category && found_font.style == font[i][10]) {
						count_fonts++;
					}
				}
				document.getElementById("nr_of_fonts").innerHTML = count_fonts + " fonts found";
			}
			
			function set_properties (id) {
				var fontcategory = font[id][3];
				var fontstyle_temp;
				var name = font[id][0];
				var fontweight = font[id][9];
				var fontstyle = font[id][10];
				var link = font[id][11];
				if (fontstyle == "Regular") {
					fontstyle_temp = "";
				}
				else {
					fontstyle_temp = fontstyle;
				}
				document.getElementById("font_name").innerHTML = name + " <b> " + fontweight + " " + fontstyle_temp + "</b>";
				document.getElementById("favorite_font").innerHTML = "<a id='heart_img' onclick='add_favorite(" + id + ");' class='heart'><img src='img/heart1.png' border='0' onMouseOver='this.src=\"img/heart2.png\";' onMouseOut='this.src=\"img/heart1.png\";'></a>";
				
				find_related (id);
				
				// reset search field
				document.getElementById("search_field").value = "search for a font...";
				search_font();	
			}
			
			
			function set_slides (id) {
				for (j = 1; j < 6; j++) {
						document.getElementById("slider_" + j).style.left = font[id][j + 3] + "px";
				}
			}
			
			
			
			////////////////////////////////////////////////////////////////////
			// III. editor functions
			
			
			function change_size () {
			    var a = ExtractNumber (document.getElementById("size_slider").style.left);
			     if (this_field == 1) { a *= 2; }
			    textfield[this_field].size = a;
			    var height = correct_height (a, found_font.height);
			    document.getElementById("textfield_" + this_field).style.fontSize = height + "px";
			    change_line_height ()
			}
			
			
			function change_line_height () {
			    var a = ExtractNumber (document.getElementById("size_slider").style.left);
			    if (this_field == 1) { a *= 2; }
			    var b = correct_height (a, found_font.height);
			    var c = ExtractNumber (document.getElementById("line_height_slider").style.left);
			    textfield[this_field].line = c;
			    var d = (c + 50) * b / 100
			    document.getElementById("textfield_" + this_field).style.lineHeight = d + "px";
			}
			
			function change_color () {
				var c = ExtractNumber (document.getElementById("c").style.left);
				var m = ExtractNumber (document.getElementById("m").style.left);
				var y = ExtractNumber (document.getElementById("y").style.left);
				var k = ExtractNumber (document.getElementById("k").style.left);
				textfield[this_field].color = [c, m, y, k];
				
				var color = cmyk_to_rgb (c, m, y, k);
				document.getElementById("textfield_" + this_field).style.color = "#" + color;
			}
			
			function cmyk_to_rgb (c, m, y, k) {
				c /= 100;
				m /= 100;
				y /= 100;
				k /= 100;
				var r = Math.round (255 * (1 - c) * (1 - k));
				var g = Math.round (255 * (1 - m) * (1 - k));
				var b = Math.round (255 * (1 - y) * (1 - k));
					
				r = r.toString(16);
				g = g.toString(16);
				b = b.toString(16);
				
				if (r.length == 1) { r = "0" + r; }
				if (g.length == 1) { g = "0" + g; }
				if (b.length == 1) { b = "0" + b; }
				
				var color = r+g+b;
				return color;
			}
			
			function set_color_slides (field) {
				var c = textfield[field].color[0];
				var m = textfield[field].color[1];
				var y = textfield[field].color[2];
				var k = textfield[field].color[3];
				document.getElementById("c").style.left = c + "px";
				document.getElementById("m").style.left = m + "px";
				document.getElementById("y").style.left = y + "px";
				document.getElementById("k").style.left = k + "px";
			}
			
			function set_editor_slides (field) {
				var a = textfield[field].size;
				if (this_field == 1) { a /= 2; }
				document.getElementById("size_slider").style.left = a + "px";
				document.getElementById("line_height_slider").style.left = textfield[field].line + "px";
			}
			
			function text_align (side) {
				textfield[this_field].align = side;
				document.getElementById("textfield_" + this_field).style.textAlign = side;
				set_align (this_field);
			}
			
			function set_align (field) {
				var align = new Array ("left", "center", "right");
				var text = "";
				for (i = 0; i < 3; i++) {
					var side = align[i];
					if (textfield[field].align == side ) {
						text += '<img src="img/align-' + side + '3.png" border="0">&nbsp;';					
					}
					else {
						text += '<a onclick="text_align(\'' + side + '\');"><img src="img/align-' + side + '1.png" border="0" onmouseover="this.src=\'img/align-' + side + '2.png\';"  onmouseout="this.src=\'img/align-' + side + '1.png\';"></a>&nbsp;';
					}
				}
				document.getElementById("text_align").innerHTML = text;
			}
			
			
			function correct_height (wanted_height, original_height) {
				var height = Math.round ((wanted_height + 32) * 150 / original_height);
				found_font.correct_height = height;
				return height;
			}
				
						
			function field_focus (q) {
				this_field = q;
				var id = textfield[q].id;
				textfield[q].focus = 1;
				document.getElementById("textfield_" + q).focus();
				set_properties(id);
				set_slides (id);
				set_editor_slides (q);
				set_align(q);
				set_color_slides (q);
				show_chosen_lines (q);
				change_fontcategory (font[id][3]);
				change_fontstyle (font[id][10]);
			}
			
			
			function show_focus_lines (q) {
				for (i = 2; i < 7; i++) {
					document.getElementById("textcontainer_" + i).style.border = "1px solid #fff";
					document.getElementById("handle_" + i).style.backgroundColor = "transparent";
				}
				if (q > 1) {
					document.getElementById("textcontainer_" + q).style.outline = "0px";
					document.getElementById("textcontainer_" + q).style.border = "1px solid #E6BE00";
					document.getElementById("handle_" + q).style.backgroundColor = "#E6BE00";
				}

				if (q != this_field) {
					document.getElementById("textcontainer_" + this_field).style.border = "1px dotted #6D6F71";
				}
			}
			
			function show_chosen_lines (q) {
				for (i = 2; i < 7; i++) {
					document.getElementById("textcontainer_" + i).style.border = "1px solid #fff";
				}
				if (q > 1) {
					document.getElementById("textcontainer_" + q).style.border = "1px dotted #6D6F71";
				}
			}
			
			function download_font () {
				var id = textfield[this_field].id;
				var link = font[id][11];
				window.open (link, '_blank');
			}
			
			function get_url () {
				var id = textfield[this_field].id;
				var link = "http://www.fontapp.org?id=" + id;
				if (document.getElementById("textfield_1").innerHTML != "The quick brown fox jumps over the lazy dog") {
					link += "&text=" + document.getElementById("textfield_1").innerHTML;
				}
				if (textfield[1].align != "left") {
					link += "&align=" + textfield[1].align;
				}
				if (textfield[1].size != 200) {
					link += "&size=" + textfield[1].size;
				}
				if (textfield[1].line != 50) {
					link += "&line=" + textfield[1].line;
				}
				if (textfield[1].color != "0,0,0,100") {
					link += "&color=" + textfield[1].color;
				}
				document.getElementById("url").style.display = "block";
				document.getElementById("url").value = link;
				document.getElementById("url").select();
			}
			
			////////////////////////////////////////////////////////////////////
			// IV. left menu
			
			
			function change_fontcategory (i) {
			    found_font.category = i;
			    document.getElementById("selected_1").innerHTML = "<a id='a_selected_1' onclick='drop_out(1);'>" + i.substr(4) + "</a>";
			    if (drops[1] == 1) {
				    drop_out(1);
			    }
			    count_fonts ();
			}
			
			function change_fontstyle (i) {
			    found_font.style = i;
			    document.getElementById("selected_2").innerHTML = "<a id='a_selected_2' onclick='drop_out(2);'>" + i + "</a>";
			    if (drops[2] == 1) {
				    drop_out(2);
			    }
			    count_fonts ();
			}
			
			
			
			
			////////////////////////////////////////////////////////////////////
			// V. top menu functions
			
			function view_mode (q) {
			    this_mode = q;
			    // move paper
			    resize_paper ();
			    reposition_paper ();
			    
			    // set button
			    for (i = 0; i < 3; i++) {
				    document.getElementById("viewmode_" + i).className = "fav_and_rel";	
			    }
			    document.getElementById("viewmode_" + q).className = "chosen_mode";
			    
			    // move menu
			    if (q == 0) {
				    var a = "a";
				    var y = "560px";
				    var overflow = "hidden";
				    document.getElementById("url_img").style.display = "inline";
			    }
			    if (q > 0) {
				    var a = "b";
				    var y = "145px";
				    var overflow = "visible";
				    document.getElementById("url").style.display = "none";
				    document.getElementById("url_img").style.display = "none";
			    }
			    document.getElementById("block_container").style.top = y;
			    document.getElementById("paper").style.overflow = overflow;
			    for (i = 1; i < 4; i++) {
				    document.getElementById("block_" + i).className = "block_" + a;
			    }
			    
			    // set fields
			    for (i = 1; i < 7; i++) {
				if (mode[this_mode].field.indexOf(i) != -1) {
				    document.getElementById("textcontainer_" + i).style.display = "block";
				}
				else {
				    document.getElementById("textcontainer_" + i).style.display = "none";
				}
			    }
			    // focus field
			    this_field = mode[this_mode].field[0];
			    document.getElementById("textfield_" + this_field).focus();
			    field_focus (this_field);
			    show_colorbox("hide");
			}
			
	
			function search_font (){
			    var j = 0;
			    var search = document.getElementById("search_field").value.toLowerCase();
			    var text = "";
			    var a = "";
			    if (search.length > 0) {
				    for (var i = 1; i < font.length; i++) {
					    needle = font[i][0].toLowerCase();
					    if (needle.indexOf(search) > -1) {
						    if (j < 15) {
							    if (font[i][10] != "Regular") {
								    a = " " + font[i][10];
							    }
							    else {
								    a = "";
							    }
							    text += "<a onclick='change_one(" + i + ");' class='search'>" + font[i][0] +" " + font[i][9] + a + "</a>";
						    }
						    j++;
					    }
				    }
			    }
			    if (j > 14) {
				    text += "<br><font style='padding-left:16px;'>... and " + j + " more fonts</font><br><br>";
			    }
			    document.getElementById("search_results").innerHTML = text;
			    if (text != "") {
				    document.getElementById("search_results").style.visibility = "visible";
			    }
			    else {
				    document.getElementById("search_results").style.visibility = "hidden";
			    }
				
			}
			
			function find_related (id) {
				document.getElementById("top_item_3").innerHTML = "";
				var text = "";
				for (var i = 1; i < font.length; i++) {
					if (font[id][0] == font[i][0] && id != i) {
						var a = "";
						if (font[i][10] != "Regular") {
							a = " " + font[i][10];
						}
						text += "<a onclick='change_one(" + i + ");' class='fav_and_rel'>" + font[i][0] + " " + font[i][9] + a + "</a>";
					}
				}
				if (text == "") {
					document.getElementById("top_item_3").innerHTML = "<i>No related fonts</i>";
				}
				else {
					document.getElementById("top_item_3").innerHTML = text;
				}
			}
			
			function add_favorite (id) {
				var l = favorites.length;
				var hit = 0;
				for (i = 0; i < l; i++) {
					if (favorites[i] == id) {
						hit = 1;
					}
				}
				if (hit == 0) {
					favorites[l] = id;
					show_favorites ();
					if (top_items[2] == 0) {
						document.getElementById("label_small_2").style.background = "#fff";
						setTimeout( function() {
							document.getElementById("label_small_2").style.background = "#6D6F71";
						},100);
					}
				}
				else {
					document.getElementById("message").style.visibility = "visible";
					document.getElementById("message_txt").innerHTML = "Font already added. Check the Favourites tab at the right of your window.";
				}
			}
			
			function remove_favorite (i) {
				favorites[i] = "";
				show_favorites();
			}
			
			function show_favorites () {
				var text = "";	
				for (i = 0; i < favorites.length; i++) {
					if (favorites[i] != "") {
						var a = "";
						if (font[favorites[i]][10]  != "Regular") {
							a = " " + font[favorites[i]][10] ;
						}
						text += "<a onclick='change_one (" +  favorites[i] + ");' class='fav_and_rel'>" + font[favorites[i]][0] + " " + font[favorites[i]][9] + a + "</a>&nbsp;&nbsp;<a onclick='remove_favorite(" +  i + ");' class='close_fav'>x</a>";
					}
				}
				document.getElementById("top_item_2").innerHTML = text;
			}
			
			
			function change_one (id) {
				make_text(id);
				set_slides(id);
				change_fontcategory (found_font.category);
				change_fontstyle (found_font.style);
			}
			

			
			////////////////////////////////////////////////////////////////////
			// VI. Other functions
			
			function show_colorbox (a) {
				if (colorbox == 1 || a == "hide") {
					document.getElementById("colorbox").style.display = "none";
					document.getElementById("color_button").src = "img/color1.png";
					colorbox = 0;
				}
				else {
					document.getElementById("colorbox").style.display = "block";
					document.getElementById("color_button").src = "img/color2.png";
					colorbox = 1;

				}
			}
			
			function deactivate (q) {	
				if (properties[q] == 1) {
					document.getElementById("parameter_" + q).className = "box_inactive";
					properties[q] = 0;
				}
				else {
					document.getElementById("parameter_" + q).className = "box";
					properties[q] = 1;
				}
			}
			
			
			function close_message () {	
				document.getElementById("message").style.visibility = "hidden";
			}
			
			
			function close_top_item (i) {
				if (top_items[i] == 1) {
					document.getElementById("close_top_" + i).innerHTML = "&#8592;";
					document.getElementById("top_item_" + i).style.display = "none";
					document.getElementById("cover_" + i).style.display = "none";
					document.getElementById("label_" + i).style.left = "290px";
					top_items[i] = 0; 
				}
				else {
					document.getElementById("close_top_" + i).innerHTML = "X";
					document.getElementById("top_item_" + i).style.display = "block";
					document.getElementById("cover_" + i).style.display = "block";
					document.getElementById("label_" + i).style.left = "0px";
					top_items[i] = 1; 
				}
			}
			
			function drop_out (i) {
				if (drops[i] == 0) {
					document.getElementById("list_" + i).style.display = "block";
					document.getElementById("a_selected_" + i).style.backgroundImage = "url('img/arrow3.png')";
					drops[i] = 1;
				}
				else {
					document.getElementById("list_" + i).style.display = "none";
					document.getElementById("a_selected_" + i).style.backgroundImage = "url('img/arrow.png')";
					drops[i] = 0;
				}
			}
			
			function resize_paper () {
			    document.getElementById("paper").style.height = mode[this_mode].height + "px";
			    document.getElementById("paper").style.width = mode[this_mode].width + "px";
			    
			}
			
			function reposition_paper () {
			    document.getElementById("paper").style.left = mode[this_mode].x + "px";
			    document.getElementById("paper").style.top = mode[this_mode].y + "px";
			}		
		</script>
	</HEAD>

	<BODY>
	    
	<script type="text/javascript">
	$(document).ready (function() {
		var $window = jQuery(window);
		function checkWidth() {
		    var width = jQuery(window).width() - 60;
		    var height = jQuery(window).height() - 140;
		    mode[0].width = width;
		    mode[1].height = height;
		    mode[1].width = height * 0.7;
		    resize_paper();
		}
		init();
		checkWidth();
		jQuery(window).resize(checkWidth);
	});
	
	$(function() { $('.resizable').draggable( {handle: ".handle" }).resizable();});
	</script>
		

		<?php include "includes/menu.php";?>
		
	<div id="main_container" style="width:100%; height:100%;">		
		
		
		<div id="paper">
			<div id="textcontainer_1">
				<div id="textfield_1" contenteditable="true"  onfocus="field_focus(1);"><?php echo $text_1 ?></div>
			</div>
			
			<div id="textcontainer_2" class="resizable" onclick="field_focus(2);" style="position:absolute; top:50px;" onmousemove="show_focus_lines(2);" onmouseout="show_focus_lines(0);">
				<div id="handle_2" class="handle">&nbsp;</div>
				<div class="textfield" id="textfield_2" contenteditable="true"><?php echo $text_2 ?></div>	
			</div>
			
			<div id="textcontainer_3" class="resizable" onclick="field_focus(3);" style="position:absolute; top:170px;" onmousemove="show_focus_lines(3);" onmouseout="show_focus_lines(0);">
				<div id="handle_3" class="handle">&nbsp;</div>
				<div class="textfield" id="textfield_3" contenteditable="true"><?php echo $text_3 ?></div>	
			</div>
			
			<div id="textcontainer_4" class="resizable" onclick="field_focus(4);" style="position:absolute; top:400px;" onmousemove="show_focus_lines(4);" onmouseout="show_focus_lines(0);">
				<div id="handle_4" class="handle">&nbsp;</div>
				<div class="textfield" id="textfield_4" contenteditable="true"><?php echo $text_4 ?></div>	
			</div>
			
			<div id="textcontainer_5" class="resizable" onclick="field_focus(5);" style="position:absolute; top:40px;" onmousemove="show_focus_lines(5);" onmouseout="show_focus_lines(0);">
				<div id="handle_5" class="handle">&nbsp;</div>
				<div class="textfield" id="textfield_5" contenteditable="true"><?php echo $text_5 ?></div>	
			</div>
			
			<div id="textcontainer_6" class="resizable" onclick="field_focus(6);" style="position:absolute; top:120px;" onmousemove="show_focus_lines(6);" onmouseout="show_focus_lines(0);">
				<div id="handle_6" class="handle">&nbsp;</div>
				<div class="textfield" id="textfield_6" contenteditable="true"><?php echo $text_6 ?></div>	
			</div>
		</div>
		
		
		
		<div id="block_container" style="position:absolute; left:8px; top:560px;">
		    
		    <div id="block_1" class="block_a" style="width:260px;">
				<div class="action">&#8594; 01. Choose category and style</div>
				<div id="selected_1">
				    <a id="a_selected_1" onclick='drop_out(1);'>Sans Serif</a>
						

				</div>
				
				<div id="list_1">
				    <a onclick='change_fontcategory("01. Sans Serif"); find_best_match(0);'>Sans Serif</a>
				    <a onclick='change_fontcategory("02. Serif"); find_best_match(0);'>Serif</a>
				    <a onclick='change_fontcategory("03. Slab Serif"); find_best_match(0);'>Slab Serif</a>
				    <a onclick='change_fontcategory("04. Monospace"); find_best_match(0);'>Monospace</a>
				    <a onclick='change_fontcategory("05. Script"); find_best_match(0);'>Script</a>
				    <a onclick='change_fontcategory("06. Fun"); find_best_match(0);'>Fun</a>
				    <a onclick='change_fontcategory("07. All Caps"); find_best_match(0);'>All Caps</a>
				</div>
				
					
				<div id="selected_2">
				    <a id="a_selected_2" onclick='drop_out(2);'>Regular</a>
				</div>
				
				<div id="list_2">
				       <a onclick='change_fontstyle("Regular"); find_best_match(0);'>Regular</a>
				       <a onclick='change_fontstyle("Italic"); find_best_match(0);'>Italic</a>
			       </div>
				
				<div id="nr_of_fonts">
					... fonts found
				</div>
		    </div>
		    
		    <div id="block_2" style="width:260px;" class="block_a">
			<div class="action">&#8594; 02. Set the properties for the font</div>
			<?php
			$parameter = array ("", "Boldness", "Serif size", "Width", "Roundness", "Ascender");
			for ($i = 1; $i < 6; $i++) {
				echo "
				<div id='parameter_".$i."' class='box'>
					".$parameter[$i]."
					<div class='property_container'>
						<div class='property_line'></div>
						<div id='slider_".$i."' class='drag' style='left:52px;' on></div>
						<div id='hide_show_".$i."' class='close' onclick='deactivate(".$i."); find_best_match(0);'></div>
					</div>
				</div>";
			}
			?>
		    </div>
		    
		    <div id="block_3" class="block_a" style="width:260px;">
			<div class="action">&#8594; 03. This is your font!</div>
			<div style="position:relative;">
			    <div id="font_name" style="width:195px; height:20px">...</div>
			    <div id="favorite_font" style="position:absolute; top:0px; left:230px;">...</div>
			    
			</div>
		    
		    
			<div id='text_size' class='box'>
				Text size
				<div class='property_container'>
					<div class='property_line'></div>
					<div id='size_slider' class='drag' style='left:100px;' on></div>
				</div>
			</div>
			
			<div id='line_height' class='box'>
				Line-height
				<div class='property_container'>
					<div class='property_line'></div>
					<div id='line_height_slider' class='drag' style='left:50px;' on></div>
				</div>
			</div>
			<div id="editor">
				<div id="text_align">
					...
				</div>
				<a onclick="show_colorbox();"><img id="color_button" src="img/color1.png" border="0"></a>
			</div>
			
			<div id="colorbox">
				<div id='color_1' class='colorbox'>
					C
					<div class='property_container'>
						<div class='property_line'></div>
						<div id='c' class='drag' style='left:0px;' on></div>
					</div>
				</div>
				<div id='color_2' class='colorbox'>
					M
					<div class='property_container'>
						<div class='property_line'></div>
						<div id='m' class='drag' style='left:0px;' on></div>
					</div>
				</div>
				<div id='color_3' class='colorbox'>
					Y
					<div class='property_container'>
						<div class='property_line'></div>
						<div id='y' class='drag' style='left:0px;' on></div>
					</div>
				</div>
				<div id='color_1' class='colorbox'>
					K
					<div class='property_container'>
						<div class='property_line'></div>
						<div id='k' class='drag' style='left:100px;' on></div>
					</div>
				</div>
			
			</div>
			
			<div id="downloadbox">
				<a id='download_img' onclick="download_font();"><img src='img/download1.png' border='0' onMouseOver='this.src="img/download2.png";' onMouseOut='this.src="img/download1.png";'></a>&nbsp;&nbsp;
				<a id='url_img' onclick="get_url();"><img src='img/url1.png' border='0' onMouseOver='this.src="img/url2.png";' onMouseOut='this.src="img/url1.png";'></a><br>
				<input type="text" id="url" value="">
			</div>
		    </div>
		    
		    
		    
		</div>
		
		
		
		<div id="topmenu" style="position: absolute; z-index:10; right:0px; top:40px;width:400px;">
		    <div style="position:relative; margin-bottom:10px;">
			    <div class="label_container" id="label_4" style="position:relative; width:108px;">
				    <div class="label" onClick="close_top_item(4);" onmouseover="document.getElementById('close_top_4').style.background='#fff';" onmouseout="document.getElementById('close_top_4').style.background='#F2F0E6';">Mode</div>
				    <div class="close_top" id="close_top_4" onClick="close_top_item(4); this.style.background='#fff';" onmouseover="this.style.background='#fff';" onmouseout="this.style.background='#F2F0E6';">X</div>
			    </div>
			    <div class="cover" id="cover_4">&nbsp;</div>
			    <div class="topitem" id="top_item_4">
				    <a id="viewmode_0" onclick="view_mode(0);" class='chosen_mode'>Specimen mode</a>
				    <a id="viewmode_1" onclick="view_mode(1);" class='fav_and_rel'>Poster mode</a>
				    <a id="viewmode_2" onclick="view_mode(2);" class='fav_and_rel'>Business card mode</a>
			    </div>
		    </div>			
		    
		    
		    <div style="position:relative; margin-bottom:10px;">
			    <div class="label_container" id="label_1" style="position:relative; width:108px;">
				    <div class="label" onClick="close_top_item(1);" onmouseover="document.getElementById('close_top_1').style.background='#fff';" onmouseout="document.getElementById('close_top_1').style.background='#F2F0E6';">Search</div>
				    <div class="close_top" id="close_top_1" onClick="close_top_item(1); this.style.background='#fff';" onmouseover="this.style.background='#fff';" onmouseout="this.style.background='#F2F0E6';">X</div>
			    </div>
			    <div class="cover" id="cover_1">&nbsp;</div>
			    <div class="topitem" id="top_item_1">
				    <input onfocus="if (this.value == 'search for a font...') { this.value=''; }" size="80" id="search_field" type="text" value="search for a font..." onKeyUp="search_font();">
				    <div id="glass"><img src="img/search.png" border="0"></div>
				    <div id="search_results"></div>
			    </div>
		    </div>
		    
		    
		    <div style="position:relative; margin-bottom:10px;">
			    <div class="label_container" id="label_2" style="position:relative; width:108px;">
				    <div id="label_small_2" class="label" onClick="close_top_item(2);" onmouseover="document.getElementById('close_top_2').style.background='#fff';" onmouseout="document.getElementById('close_top_2').style.background='#F2F0E6';">Favourites</div>
				    <div class="close_top" id="close_top_2" onClick="close_top_item(2); this.style.background='#fff';" onmouseover="this.style.background='#fff';" onmouseout="this.style.background='#F2F0E6';">X</div>
			    </div>
			    <div class="cover" id="cover_2">&nbsp;</div>
			    <div class="topitem" id="top_item_2">Click &hearts; to add a font to you favourites list.</div>
		    </div>
		    
		    
		    <div style="position:relative; margin-bottom:10px;">
			    <div class="label_container" id="label_3" style="position:relative; width:108px;">
				    <div class="label" onClick="close_top_item(3);" onmouseover="document.getElementById('close_top_3').style.background='#fff';" onmouseout="document.getElementById('close_top_3').style.background='#F2F0E6';">Related</div>
				    <div class="close_top" id="close_top_3" onClick="close_top_item(3); this.style.background='#fff';" onmouseover="this.style.background='#fff';" onmouseout="this.style.background='#F2F0E6';">X</div>
			    </div>
			    <div class="cover" id="cover_3">&nbsp;</div>
			    <div class="topitem" id="top_item_3">Related:</div>
		    </div>
			
		</div>
		
		
		<div id="loading">
			<img src="img/loading.gif">
		</div>
		
		
		<div id="message">
		    <div id="message_txt">
			    -
		    </div>	
		    <div style="text-align:right;">
			    <br><a onclick="close_message();" class="close_message">OK</a>
		    </div>
		</div>
		
		

		
		<div id="test" style="position:absolute; left:300px; top:10px; color:#000; display: block;">
		</div>
		

<?php
include ('includes/footer.php');
?>