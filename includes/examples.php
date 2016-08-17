<?php

//Writing
$writing = wp_insert_term('Writing', 'mbt_genre', array('slug' => 'writing'));
if(!is_wp_error($writing)) { $writing_id = array($writing['term_id']); }

//Randy Ingermanson
$ringermanson = wp_insert_term('Randy Ingermanson', 'mbt_author', array('slug' => 'ringermanson', 'description' => "Randy Ingermanson is the award-winning author of six novels. He is known around the world as \"the Snowflake Guy,\" thanks to his Web site article on the Snowflake method, which has been viewed more than a million times. Before venturing into fiction, Randy earned a Ph.D. in theoretical physics from the University of California at Berkeley. Randy has taught fiction at numerous writing conferences and sits on the advisory board of American Christian Fiction Writers. He also publishes the world's largest e-zine on how to write fiction, The Advanced Fiction Writing E-zine. Randy's first two novels won Christy awards, and his second novel Oxygen, coauthored with John B. Olson, earned a spot on the New York Public Library's Books for the Teen Age list."));
if(!is_wp_error($ringermanson)) { $ringermanson_id = array($ringermanson['term_id']); }

//Writing Fiction for Dummies
$post_id = wp_insert_post(array(
	'post_title' => 'Writing Fiction for Dummies',
	'post_content' => "So you want to write a novel? Great! That's a worthy goal, no matter what your reason. But don't settle for just writing a novel. Aim high. Write a novel that you intend to sell to a publisher. <em>Writing Fiction for Dummies</em> is a complete guide designed to coach you every step along the path from beginning writer to royalty-earning author. Here are some things you'll learn in Writing Fiction for Dummies:\n\nStrategic Planning: Pinpoint where you are on the roadmap to publication; discover what every reader desperately wants from a story; home in on a marketable category; choose from among the four most common creative styles; and learn the self-management methods of professional writers.\n\nWriting Powerful Fiction: Construct a story world that rings true; create believable, unpredictable characters; build a strong plot with all six layers of complexity of a modern novel; and infuse it all with a strong theme.\n\nSelf-Editing Your Novel: Psychoanalyze your characters to bring them fully to life; edit your story structure from the top down; fix broken scenes; and polish your action and dialogue.\n\nFinding An Agent and Getting Published: Write a query letter, a synopsis, and a proposal; pitch your work to agents and editors without fear.\n\nWriting Fiction For Dummies takes you from being a writer to being an author. It can happen—if you have the talent and persistence to do what you need to do.",
	'post_excerpt' => 'The Most Wished For book in the Fiction Writing Reference category on Amazon is Writing Fiction for Dummies, the complete guide for writing and selling your novel.',
	'post_status' => 'publish',
	'post_type' => 'mbt_book'
));
if(!is_wp_error($writing)) { wp_set_post_terms($post_id, $writing_id, 'mbt_genre'); }
if(!is_wp_error($ringermanson)) { wp_set_post_terms($post_id, $ringermanson_id, 'mbt_author'); }
if(!is_wp_error($recommended)) { wp_set_post_terms($post_id, $recommended_id, 'mbt_tag'); }
update_post_meta($post_id, 'mbt_buybuttons', unserialize('a:2:{i:0;a:3:{s:7:"display";s:6:"button";s:5:"store";s:3:"bnn";s:3:"url";s:106:"http://www.barnesandnoble.com/w/writing-fiction-for-dummies-randy-ingermanson/1100297881?ean=9780470530702";}i:1;a:3:{s:7:"display";s:6:"button";s:5:"store";s:6:"amazon";s:3:"url";s:78:"http://www.amazon.com/Writing-Fiction-Dummies-Randy-Ingermanson/dp/0470530707/";}}'));
update_post_meta($post_id, 'mbt_book_image_id', mbt_copy_and_insert_attachment(plugins_url('images/examples/writingfiction.jpg', dirname(__FILE__))));
update_post_meta($post_id, 'mbt_book_teaser', 'A complete guide to writing and selling your novel!');
update_post_meta($post_id, 'mbt_endorsements', unserialize('a:1:{i:0;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:65:"...an easy-to-follow guide providing step-by-step instructions...";s:4:"name";s:13:"Writers Forum";s:8:"name_url";s:0:"";}}'));
update_post_meta($post_id, 'mbt_unique_id_isbn', '9780470530702');
update_post_meta($post_id, 'mbt_unique_id_asin', '0470530707');
update_post_meta($post_id, 'mbt_show_unique_id', 'true');



//Web Design
$webdesign = wp_insert_term('Web Design', 'mbt_genre', array('slug' => 'web-design'));
if(!is_wp_error($webdesign)) { $webdesign_id = array($webdesign['term_id']); }

//Christopher Schmitt
$cschmitt = wp_insert_term('Christopher Schmitt', 'mbt_author', array('slug' => 'cschmitt', 'description' => "Christopher Schmitt is the founder of Heatvision.com, Inc., an Austin-based new media publishing and design firm. An award-winning web designer who has been working in the medium for twenty years, Christopher interned for both David Siegel and Lynda Weinman as an undergraduate at Florida State University. He has a Masters in Communication for Interactive and New Communication Technologies, and is the author of six books, Including CSS Cookbook, which was named Best Web Design Book of 2006."));
if(!is_wp_error($cschmitt)) { $cschmitt_id = array($cschmitt['term_id']); }

//Designing Web & Mobile Graphics
$post_id = wp_insert_post(array(
	'post_title' => 'Designing Web & Mobile Graphics',
	'post_content' => 'Graphics are key to the user experience of online content, especially now that users are accessing that content on a multitude of devices: smartphones, tablets, laptops, and desktops. This book provides foundational methodology for optimal use of graphics that begins with HTML and CSS, and delves into the worlds of typography, color, transparency, accessibility, imagery, and layout for optimal delivery on all the different devices people use today.It serves beginners and intermediate web builders alike with a complete foundation needed to create successful illustrative and navigational imagery for web and mobile. Coverage includes:<ul>	<li>lessons on typography, icons, color, and images</li>	<li>the latest information on HTML5, CSS3, and other modern technologies</li>	<li>in-depth exploration of image formats: GIF, PNG, JPEG, and SVG</li>	<li>ways to employ adaptive strategies for responsive web design</li></ul>',
	'post_excerpt' => 'Designing Web & Mobile Graphics provides foundational methodology for optimal use of graphics that begins with HTML and CSS, and delves into the worlds of typography, color, transparency, accessibility, imagery, and layout for optimal delivery on all the different devices people use today.',
	'post_status' => 'publish',
	'post_type' => 'mbt_book'
));
if(!is_wp_error($webdesign)) { wp_set_post_terms($post_id, $webdesign_id, 'mbt_genre'); }
if(!is_wp_error($cschmitt)) { wp_set_post_terms($post_id, $cschmitt_id, 'mbt_author'); }
if(!is_wp_error($recommended)) { wp_set_post_terms($post_id, $recommended_id, 'mbt_tag'); }
update_post_meta($post_id, "mbt_buybuttons", unserialize('a:2:{i:0;a:3:{s:7:"display";s:6:"button";s:5:"store";s:3:"bnn";s:3:"url";s:114:"http://www.barnesandnoble.com/w/designing-web-and-mobile-graphics-christopher-schmitt/1111631892?ean=9780321858542";}i:1;a:3:{s:7:"display";s:6:"button";s:5:"store";s:6:"amazon";s:3:"url";s:78:"http://www.amazon.com/Designing-Web-Mobile-Graphics-Fundamental/dp/0321858549/";}}'));
update_post_meta($post_id, "mbt_book_image_id", mbt_copy_and_insert_attachment(plugins_url('images/examples/designinggraphics.jpg', dirname(__FILE__))));
update_post_meta($post_id, 'mbt_book_teaser', 'Fundamental concepts for web and interactive projects');
update_post_meta($post_id, 'mbt_endorsements', unserialize('a:3:{i:0;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:91:"If you\'re looking for a comprehensive guide to getting started with web design, this is it.";s:4:"name";s:59:"Marc Grabanski, Founder of Frontend Masters Workshop Series";s:8:"name_url";s:0:"";}i:1;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:323:"In his book, Designing Web & Mobile Graphics, Christopher Schmitt uses easy-to-understand language to guide you through the process of designing web and mobile experiences. This book is a fantastic way to learn the nuts and bolts that you can practically apply to create experiences both for the Web and for mobile devices.";s:4:"name";s:52:"Dr. Leslie Jensen-Inman, Co-Founder of Center Centre";s:8:"name_url";s:0:"";}i:2;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:109:"If you\'re a designer trying to make sense of all the new breed of web technologies, this is the book for you.";s:4:"name";s:62:"Chris Mills, Open Standards Evangelist and Editor of dev.opera";s:8:"name_url";s:0:"";}}'));
update_post_meta($post_id, 'mbt_unique_id_isbn', '9780321858542');
update_post_meta($post_id, 'mbt_unique_id_asin', '0321858549');
update_post_meta($post_id, 'mbt_show_unique_id', 'true');



//Inspirational
$inspirational = wp_insert_term('Inspirational', 'mbt_genre', array('slug' => 'inspirational'));
if(!is_wp_error($inspirational)) { $inspirational_id = array($inspirational['term_id']); }

//N. J. Lindquist
$njlindquist = wp_insert_term('N. J. Lindquist', 'mbt_author', array('slug' => 'njlindquist', 'description' => 'N. J. Lindquist is the creator and publisher of the best-selling Hot Apple Cider books, and the author of the award-winning The Circle of Friends YA series and the standalone novel, In Time of Trouble. In past lives, she has been a high school Teacher of the Year, a homeschooler, part of several church planting teams, a youth leader, a CE Director, a church planter, a small group leader, and founder and executive director of a national writers group. She has been speaking to groups of all sizes since she was in her early twenties with messages of hope, creativity, leadership, and making disciples.'));
if(!is_wp_error($njlindquist)) { $njlindquist_id = array($njlindquist['term_id']); }

//A Second Cup of Hot Apple Cider
$post_id = wp_insert_post(array(
	'post_title' => 'A Second Cup of Hot Apple Cider',
	'post_content' => '<em>A Second Cup of Hot Apple Cider</em> is the follow-up to the bestseller,<em> Hot Apple Cider: Words to Stir the Heart and Warm the Soul</em>. <em>A Second Cup</em> won the 2012 Christian Small Publishers Gift Book Award Winner along with six 2012 The Word Awards and seven Awards of Merit. Midwest Book Review called <em>A Second Cup</em> a "reminder that there is something good in the world." <em>Faith Today</em> magazine\'s review said, "Some books surprise you with their ability to take your breath away... The short selections make this a perfect book for even indifferent readers. It would be a fabulous addition to an office waiting room, your bedside table, briefcase, backpack or purse. But be sure to buy more than one, for you will probably have the urge to share this gem of a collection with others." Please note that there is also a Discussion Guide for the book, with questions related to each story. Each contributor has supplied questions about his or her piece to help readers think further about the issues raised, enjoy stimulating discussions, and share ideas and meaningful experiences. Foreword by Ellen Vaughn.',
	'post_excerpt' => 'A collection of over 50 true stories, fictional short stories, and poems by 37 writers whose work is distinguished by honesty and vulnerability, combined with encouragement and hope.',
	'post_status' => 'publish',
	'post_type' => 'mbt_book'
));
if(!is_wp_error($inspirational)) { wp_set_post_terms($post_id, $inspirational_id, 'mbt_genre'); }
if(!is_wp_error($njlindquist)) { wp_set_post_terms($post_id, $njlindquist_id, 'mbt_author'); }
if(!is_wp_error($recommended)) { wp_set_post_terms($post_id, $recommended_id, 'mbt_tag'); }
update_post_meta($post_id, "mbt_buybuttons", unserialize('a:2:{i:0;a:3:{s:7:"display";s:6:"button";s:5:"store";s:3:"bnn";s:3:"url";s:106:"http://www.barnesandnoble.com/w/a-second-cup-of-hot-apple-cider-n-j-lindquist/1115136868?ean=9780978496319";}i:1;a:3:{s:7:"display";s:6:"button";s:5:"store";s:6:"amazon";s:3:"url";s:63:"http://www.amazon.com/Second-Cup-Hot-Apple-Cider/dp/0978496310/";}}'));
update_post_meta($post_id, "mbt_book_image_id", mbt_copy_and_insert_attachment(plugins_url('images/examples/hotapplecider.jpg', dirname(__FILE__))));
update_post_meta($post_id, 'mbt_book_teaser', 'Words to Stimulate the Mind and Delight the Spirit');
update_post_meta($post_id, 'mbt_endorsements', unserialize('a:3:{i:0;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:416:"Sometimes all you need is a reminder that there is something good in the world. A Second Cup of Hot Apple Cider is a combination of short stories, poetry, and works of memoir picked for their inspirational nature, dedicated to finding a shining light in our lives that so often turn dark. The stories within are touching and poignant, and will help readers remember that there is something after the worst of it all.";s:4:"name";s:48:"Midwest Book Review, Religion/Spirituality Shelf";s:8:"name_url";s:0:"";}i:1;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:157:"A Second Cup of Hot Apple Cider is exactly the type of warm assurance, in satisfyingly small sips, that we could all use throughout the course of a busy day.";s:4:"name";s:44:"Nicholas Forbes, ManitobaChristianOnline.com";s:8:"name_url";s:0:"";}i:2;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:129:"The very first story in A Second Cup of Hot Apple Cider left me in tears. The next stories kept me turning pages, wanting more...";s:4:"name";s:33:"The Koala Bear Writer, Bonnie Way";s:8:"name_url";s:0:"";}}'));
update_post_meta($post_id, 'mbt_unique_id_isbn', '9780978496319');
update_post_meta($post_id, 'mbt_unique_id_asin', '0978496310');
update_post_meta($post_id, 'mbt_show_unique_id', 'true');



//Thriller
$thriller = wp_insert_term('Thriller', 'mbt_genre', array('slug' => 'thriller'));
if(!is_wp_error($thriller)) { $thriller_id = array($thriller['term_id']); }

//R. E. McDermott
$remcdermott = wp_insert_term('R. E. McDermott', 'mbt_author', array('slug' => 'remcdermott', 'description' => 'R. E. McDermott is a merchant seaman by training. After graduation from the Merchant Marine Academy, he sailed for several years, and then spent thirty years as a marine consultant. He has traveled widely, seen interesting things, and lived in several countries. He has come to know more than a few interesting characters in his travels, and bits and pieces of them populate his novels.'));
if(!is_wp_error($remcdermott)) { $remcdermott_id = array($remcdermott['term_id']); }

//Deadly Straits
$post_id = wp_insert_post(array(
	'post_title' => 'Deadly Straits',
	'post_content' => 'In the tradition of Clancy and Cussler, R.E. McDermott delivers a thriller to rival the masters. When marine engineer and very part-time spook Tom Dugan becomes collateral damage in the War on Terror, he\'s not about to take it lying down. Falsely implicated in a hijacking, he\'s offered a chance to clear himself by helping the CIA snare their real prey, Dugan\'s best friend, London ship owner Alex Kairouz. Reluctantly, Dugan agrees to go undercover in Alex\'s company, despite doubts about his friend\'s guilt. Once undercover, Dugan\'s steadfast refusal to accept Alex\'s guilt puts him at odds not only with his CIA superiors, but also with a beautiful British agent with whom he\'s romantically involved. When a tanker is found adrift near Singapore with a dead crew, and another explodes in Panama as Alex lies near death after a suspicious suicide attempt, Dugan is framed for the attacks. Out of options, and convinced the attacks are prelude to an even more devastating assault, Dugan eludes capture to follow his last lead to Russia, only to be shanghaied as an \'advisor\' to a Russian Spetsnaz unit on a suicide mission. <em>Deadly Straits</em> is a non-stop thrill ride, from London streets, to the dry docks of Singapore, to the decks of the tankers that feed the world\'s thirst for oil, with stops along the way in Panama, Langley, Virginia, and Teheran. Richly spiced with detail from the author\'s 30 years sailing, building, and repairing ships worldwide, it is, in the words of one reviewer, "fast-paced, multilayered and gripping."',
	'post_excerpt' => 'When marine engineer and very part-time spook Tom Dugan becomes collateral damage in the War on Terror, he\'s not about to take it lying down. Deadly Straits is a non-stop thrill ride, fast-paced, multilayered and gripping.',
	'post_status' => 'publish',
	'post_type' => 'mbt_book'
));
if(!is_wp_error($thriller)) { wp_set_post_terms($post_id, $thriller_id, 'mbt_genre'); }
if(!is_wp_error($remcdermott)) { wp_set_post_terms($post_id, $remcdermott_id, 'mbt_author'); }
if(!is_wp_error($recommended)) { wp_set_post_terms($post_id, $recommended_id, 'mbt_tag'); }
update_post_meta($post_id, "mbt_buybuttons", unserialize('a:2:{i:0;a:3:{s:7:"display";s:6:"button";s:5:"store";s:3:"bnn";s:3:"url";s:88:"http://www.barnesandnoble.com/w/deadly-straits-re-mcdermott/1103871471?ean=9780983741701";}i:1;a:3:{s:7:"display";s:6:"button";s:5:"store";s:6:"amazon";s:3:"url";s:68:"http://www.amazon.com/Deadly-Straits-Dugan-Novel-ebook/dp/B0057AMO2A";}}'));
update_post_meta($post_id, "mbt_book_image_id", mbt_copy_and_insert_attachment(plugins_url('images/examples/deadlystraits.jpg', dirname(__FILE__))));
update_post_meta($post_id, 'mbt_book_teaser', 'The Ultimate Terrorist Attack');
update_post_meta($post_id, 'mbt_endorsements', unserialize('a:3:{i:0;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:55:"A fast-moving thriller packed with action and intrigue.";s:4:"name";s:51:"Scott Nicholson, best-selling author of Liquid Fear";s:8:"name_url";s:0:"";}i:1;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:230:"With his debut novel, McDermott sets the bar high--very high. This ambitious novel will keep you turning the pages to get to the thrilling conclusion. This is a truly impressive first book. I look forward to reading more from him.";s:4:"name";s:26:"Neal Hock, Bookhound\'s Den";s:8:"name_url";s:0:"";}i:2;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:265:"International intrigue in the hands of an expert. With his breathless pacing and punchy prose, McDermott knots a complicated plot so real it might as well be breaking news. Deadly Straits ravages like a category-five hurricane: unpredictable, merciless, and fierce.";s:4:"name";s:48:"L.C. Fiore, award-winning author of Green Gospel";s:8:"name_url";s:0:"";}}'));
update_post_meta($post_id, 'mbt_unique_id_isbn', '9780983741701');
update_post_meta($post_id, 'mbt_unique_id_asin', 'B0057AMO2A');
update_post_meta($post_id, 'mbt_show_unique_id', 'true');



//Mary DeMuth
$mdemuth = wp_insert_term('Mary DeMuth', 'mbt_author', array('slug' => 'mdemuth', 'description' => 'Mary DeMuth is the author of 17 traditionally published books, and she intimately knows and understands the publishing industry. She often speaks at major writing conferences around the United States, and she mentors writers toward publication at The Writing Spa. She lives with her husband and three teens in Texas.'));
if(!is_wp_error($mdemuth)) { $mdemuth_id = array($mdemuth['term_id']); }

//The 11 Secrets of Getting Published
$post_id = wp_insert_post(array(
	'post_title' => 'The 11 Secrets of Getting Published',
	'post_content' => "Frustrated by how much there is to learn to finally see your name in print with a big publishing house? Mired in confusion about your next steps? An accomplished nonfiction freelancer and novelist with 17 traditionally published books under her author belt, Mary DeMuth understands the twists and turns of the publishing industry. She answers the question, \"How can I get published?\" by pulling 11 Secrets from her popular blog, Wannabepublished.\n\nMary teaches you how to: <ul>	<li>Craft the kind of query letter that gets you noticed by industry professionals.</li> <li>Write stronger, powerful, attention grabbing prose.</li> <li>Create effective writing routines to meet your daily and weekly goals.</li> <li>Navigate a writing conference with confidence.</li> <li>Find and woo an agent.</li></ul>",
	'post_excerpt' => 'Learn insider secrets of getting published! Become an author. Hold the book of your heart in your hands for the first time.',
	'post_status' => 'publish',
	'post_type' => 'mbt_book'
));
if(!is_wp_error($writing)) { wp_set_post_terms($post_id, $writing_id, 'mbt_genre'); }
if(!is_wp_error($mdemuth)) { wp_set_post_terms($post_id, $mdemuth_id, 'mbt_author'); }
if(!is_wp_error($recommended)) { wp_set_post_terms($post_id, $recommended_id, 'mbt_tag'); }
update_post_meta($post_id, "mbt_buybuttons", unserialize('a:2:{i:0;a:3:{s:7:"display";s:6:"button";s:5:"store";s:3:"bnn";s:3:"url";s:104:"http://www.barnesandnoble.com/w/11-secrets-of-getting-published-mary-demuth/1102378897?ean=2940012611758";}i:1;a:3:{s:7:"display";s:6:"button";s:5:"store";s:6:"amazon";s:3:"url";s:44:"http://www.amazon.com/gp/product/098343672X/";}}'));
update_post_meta($post_id, "mbt_book_image_id", mbt_copy_and_insert_attachment(plugins_url('images/examples/11secrets.jpg', dirname(__FILE__))));
update_post_meta($post_id, 'mbt_book_teaser', 'See how easily you can learn the secrets of getting published!');
update_post_meta($post_id, 'mbt_endorsements', unserialize('a:3:{i:0;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:241:"If you want to know about the field of writing then you want to know Mary DeMuth. She is not only gifted as both a fiction and non-fiction author, but she is also passionate about helping others achieve their goals in the writing profession.";s:4:"name";s:64:"Karol Ladd, Best-selling author of The Power of a Positive Woman";s:8:"name_url";s:0:"";}i:1;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:126:"Mary is a consummate professional and well equipped to help other writers with both the creative and business side of writing.";s:4:"name";s:54:"Terry Glaspey, Senior Editor, Harvest House Publishers";s:8:"name_url";s:0:"";}i:2;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:276:"I seldom read a non-fiction from cover to cover, especially in just a couple of days, but I not only examined every piece of it, I pulled out my manuscript and took her advice to heart in a complete revision. Anyone interested in writing for publication should read this book!";s:4:"name";s:21:"Marji Clubine, writer";s:8:"name_url";s:0:"";}}'));
update_post_meta($post_id, 'mbt_unique_id_isbn', '9780983436720');
update_post_meta($post_id, 'mbt_unique_id_asin', '098343672X');
update_post_meta($post_id, 'mbt_show_unique_id', 'true');



//Christian Living
$christianliving = wp_insert_term('Christian Living', 'mbt_genre', array('slug' => 'christian-living'));
if(!is_wp_error($christianliving)) { $christianliving_id = array($christianliving['term_id']); }

//Nancy Grisham
$ngrisham = wp_insert_term('Nancy Grisham', 'mbt_author', array('slug' => 'ngrisham', 'description' => 'Nancy Grisham launched the speaking ministry Livin\' Ignited in 2004. She has been a frequent teacher at Willow Creek Community Church\'s midweek classes and an adjunct faculty member at Wheaton College. She lives in Denver, Colorado.'));
if(!is_wp_error($ngrisham)) { $ngrisham_id = array($ngrisham['term_id']); }

//Thriving: Trusting God for Life to The Fullest
$post_id = wp_insert_post(array(
	'post_title' => 'Thriving: Trusting God for Life to The Fullest',
	'post_content' => 'Jesus made believers a bold promise: life and life to the fullest. He offers us more than just barely getting by when challenges come our way. But that kind of life doesn\'t happen automatically. Thriving equips you to live the abundant life. Using personal stories and biblical insights, Nancy Grisham shows you how to appropriate Jesus\' promise in your own life, even in the midst of difficult circumstances. Each chapter concludes with a practical reflection and application section perfect for individual study or small group discussions.',
	'post_excerpt' => 'Thriving equips you to live the abundant life – even in difficult circumstances. Chapters include practical questions for individual study or small group discussion.',
	'post_status' => 'publish',
	'post_type' => 'mbt_book'
));
if(!is_wp_error($christianliving)) { wp_set_post_terms($post_id, $christianliving_id, 'mbt_genre'); }
if(!is_wp_error($ngrisham)) { wp_set_post_terms($post_id, $ngrisham_id, 'mbt_author'); }
if(!is_wp_error($recommended)) { wp_set_post_terms($post_id, $recommended_id, 'mbt_tag'); }
update_post_meta($post_id, "mbt_buybuttons", unserialize('a:2:{i:0;a:3:{s:7:"display";s:6:"button";s:5:"store";s:3:"bnn";s:3:"url";s:83:"http://www.barnesandnoble.com/w/thriving-nancy-grisham/1113451228?ean=9780801015434";}i:1;a:3:{s:7:"display";s:6:"button";s:5:"store";s:6:"amazon";s:3:"url";s:71:"http://www.amazon.com/Thriving-Trusting-God-Life-Fullest/dp/080101543X/";}}'));
update_post_meta($post_id, "mbt_book_image_id", mbt_copy_and_insert_attachment(plugins_url('images/examples/thriving.jpg', dirname(__FILE__))));
update_post_meta($post_id, 'mbt_book_teaser', 'Jesus made believers a bold promise: life and life to the fullest.');
update_post_meta($post_id, 'mbt_endorsements', unserialize('a:3:{i:0;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:82:"Want to be spiritually encouraged, enlightened, and inspired? Then open this book!";s:4:"name";s:54:"Lee Strobel, bestselling author of The Case for Christ";s:8:"name_url";s:0:"";}i:1;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:257:"It is so refreshing for someone to break through the paralyzing survival mentality and offer a strategy that enables us to thrive. The chapter \'You Are Here... and So Is God\' alone is worth the price of the book. Thriving is a treasure map to abundant life.";s:4:"name";s:54:"Ken Davis, speaker, comedian, and award-winning author";s:8:"name_url";s:0:"";}i:2;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:223:"Nancy shows us practically how to use the pain of our lives to drive us to the Lord and not away from him. \'Real faith,\' she writes, \'goes the distance with God, especially in rough terrain.\' I heartily recommend this book.";s:4:"name";s:73:"Jill Briscoe, author and minister, Elmbrook Church, Brookfield, Wisconsin";s:8:"name_url";s:0:"";}}'));
update_post_meta($post_id, 'mbt_unique_id_isbn', '9780801015434');
update_post_meta($post_id, 'mbt_unique_id_asin', '080101543X');
update_post_meta($post_id, 'mbt_show_unique_id', 'true');



//Mark Mittleberg
$mmittleberg = wp_insert_term('Mark Mittleberg', 'mbt_author', array('slug' => 'mmittleberg', 'description' => 'Mark Mittelberg is an author, speaker, and evangelism strategist. He is coauthor with Bill Hybels of <em>Becoming a Contagious Christian</em> and coauthor with Bill Hybels and Lee Strobel of the <em>Becoming a Contagious Christian</em> curriculum. He previously served as evangelism leader for the Willow Creek Association.'));
if(!is_wp_error($mmittleberg)) { $mmittleberg_id = array($mmittleberg['term_id']); }

//Confident Faith
$post_id = wp_insert_post(array(
	'post_title' => 'Confident Faith',
	'post_content' => "In <em>Confident Faith</em>, Mark Mittelberg assures Christians that we can be confident in our beliefs. There's no reason to be timid about what we believe, because our beliefs can stand up to the test. Truth isn't dependent on how a person feels or one's own point of view, as so many assert. On the contrary, we can determine truth through our five senses, and that truth reliably points to a deeper and unseen reality.\n\nMark walks readers through twenty arrows that point towards Christian beliefs: from the intricate design of the universe to archaeological proofs, from the consistent testimony of changed lives to the reliability of the ancient documents of the Bible. After studying these arrows, you'll put this book down with a renewed confidence in what you believe and why it matters for eternity.",
	'post_excerpt' => 'You can enjoy a robust faith - one you can share with skeptical friends - by learning the "Twenty Arrows of Truth," including evidence from science, logic, history, archaeology, the Bible, and more. Experience a confident faith - today!',
	'post_status' => 'publish',
	'post_type' => 'mbt_book'
));
if(!is_wp_error($christianliving)) { wp_set_post_terms($post_id, $christianliving_id, 'mbt_genre'); }
if(!is_wp_error($mmittleberg)) { wp_set_post_terms($post_id, $mmittleberg_id, 'mbt_author'); }
if(!is_wp_error($recommended)) { wp_set_post_terms($post_id, $recommended_id, 'mbt_tag'); }
update_post_meta($post_id, "mbt_buybuttons", unserialize('a:2:{i:0;a:3:{s:7:"display";s:6:"button";s:5:"store";s:3:"bnn";s:3:"url";s:92:"http://www.barnesandnoble.com/w/confident-faith-mark-mittelberg/1113896998?ean=9781414329963";}i:1;a:3:{s:7:"display";s:6:"button";s:5:"store";s:6:"amazon";s:3:"url";s:80:"http://www.amazon.com/Confident-Faith-Building-Foundation-Beliefs/dp/1414329962/";}}'));
update_post_meta($post_id, "mbt_book_image_id", mbt_copy_and_insert_attachment(plugins_url('images/examples/confidentfaith.jpg', dirname(__FILE__))));
update_post_meta($post_id, 'mbt_book_teaser', 'Are you confident in your beliefs?');
update_post_meta($post_id, 'mbt_endorsements', unserialize('a:3:{i:0;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:60:"This may well be the most important book you read this year!";s:4:"name";s:54:"Lee Strobel, Bestselling author of The Case For Christ";s:8:"name_url";s:0:"";}i:1;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:74:"Marc has emerged as a well-reasoned voice in the conversation about faith.";s:4:"name";s:86:"Max Lucado, Bestselling author of Grace: More Than We Deserve, Greater Than We Imagine";s:8:"name_url";s:0:"";}i:2;a:4:{s:8:"image_id";s:0:"";s:7:"content";s:86:"Mark guides us into understanding the importance of knowing why we believe what we do.";s:4:"name";s:96:"Dan Kimball, Author of Adventures in Churchland: Finding Jesus in the Mess of Organized Religion";s:8:"name_url";s:0:"";}}'));
update_post_meta($post_id, 'mbt_unique_id_isbn', '9781414329963');
update_post_meta($post_id, 'mbt_unique_id_asin', '1414329962');
update_post_meta($post_id, 'mbt_show_unique_id', 'true');



//Historical Fiction
$historicalfiction = wp_insert_term('Historical Fiction', 'mbt_genre', array('slug' => 'historical-fiction'));
if(!is_wp_error($historicalfiction)) { $historicalfiction_id = array($historicalfiction['term_id']); }

//Jeanette Vaughan
$jvaughan = wp_insert_term('Jeanette Vaughan', 'mbt_author', array('slug' => 'jvaughan', 'description' => 'Jeanette Vaughan is well established as a writer and story teller. Not only is she published in the periodicals and professional journals of nursing, but also in the genre of fiction. Out on her sheep farm, she has written several novels and scripts. She is the mother of four children, including two Navy pilots. She lives in a Victorian farmhouse out in the pastures of northeast Texas with her sheep, chickens, donkeys and sheep dogs.'));
if(!is_wp_error($jvaughan)) { $jvaughan_id = array($jvaughan['term_id']); }

//Flying Solo
$post_id = wp_insert_post(array(
	'post_title' => 'Flying Solo',
	'post_content' => "French Cajun Nora Broussard Greenwood was born with the wanderlust. Her adventurous spirit doesn't fit the sedate expectations of catholic 1960s New Orleans suburbia. On a whim, she takes flying lessons to become a pilot. Experiencing the freedom of flight is liberating. However an illicit affair with her pilot instructor forces action. When she confronts her ruthless husband for a divorce, she is cast out sans her children and threatened with her life. Desperate to get them back and gain liberty, she steals her husband's plane. Trials and tribulations erupt as she navigates the turbulence her life has become. In a bizarre twist of fate, she serves as caregiver to her lover's sickly wife as a means to survive; hoping he will decide she is his soul mate. But is that to be? Nora must make the make the most difficult decision of her life in order to get things back on track.",
	'post_excerpt' => 'Sometimes the choices we make in life have devastating consequences. Based on a true story, Nora is a 1960s French Cajun housewife who trains as a pilot. Dynamics collide when an ilicit affair produces a child and a choice.',
	'post_status' => 'publish',
	'post_type' => 'mbt_book'
));
if(!is_wp_error($historicalfiction)) { wp_set_post_terms($post_id, $historicalfiction_id, 'mbt_genre'); }
if(!is_wp_error($jvaughan)) { wp_set_post_terms($post_id, $jvaughan_id, 'mbt_author'); }
if(!is_wp_error($recommended)) { wp_set_post_terms($post_id, $recommended_id, 'mbt_tag'); }
update_post_meta($post_id, "mbt_buybuttons", unserialize('a:2:{i:0;a:3:{s:7:"display";s:6:"button";s:5:"store";s:3:"bnn";s:3:"url";s:89:"http://www.barnesandnoble.com/w/flying-solo-jeanette-vaughan/1115228279?ean=9780615618883";}i:1;a:3:{s:7:"display";s:6:"button";s:5:"store";s:6:"amazon";s:3:"url";s:84:"http://www.amazon.com/Flying-Solo-Unconventional-Navigates-Turbulence/dp/061561888X/";}}'));
update_post_meta($post_id, 'mbt_book_image_id', mbt_copy_and_insert_attachment(plugins_url('images/examples/flyingsolo.jpg', dirname(__FILE__))));
update_post_meta($post_id, 'mbt_book_teaser', 'An Unconventional Aviatrix Navigates Turbulence in Life');
update_post_meta($post_id, 'mbt_unique_id_isbn', '9780615618883');
update_post_meta($post_id, 'mbt_unique_id_asin', '061561888X');
update_post_meta($post_id, 'mbt_show_unique_id', 'true');
