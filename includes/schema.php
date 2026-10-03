<?php
/* Database schema + starter content used by install.php */

function schema_statements()
{
    $opt = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    return array(
        "CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(60) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(150) DEFAULT '',
            created_at DATETIME NOT NULL
        ) $opt",
        "CREATE TABLE IF NOT EXISTS categories (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            slug VARCHAR(191) NOT NULL UNIQUE,
            description VARCHAR(300) DEFAULT ''
        ) $opt",
        "CREATE TABLE IF NOT EXISTS posts (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(191) NOT NULL UNIQUE,
            excerpt VARCHAR(400) DEFAULT '',
            content LONGTEXT NOT NULL,
            image VARCHAR(255) DEFAULT '',
            category_id INT UNSIGNED DEFAULT NULL,
            meta_title VARCHAR(160) DEFAULT '',
            meta_desc VARCHAR(320) DEFAULT '',
            status VARCHAR(12) NOT NULL DEFAULT 'published',
            views INT UNSIGNED NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            KEY idx_status_date (status, created_at),
            KEY idx_cat (category_id)
        ) $opt",
        "CREATE TABLE IF NOT EXISTS pages (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            slug VARCHAR(191) NOT NULL UNIQUE,
            content LONGTEXT NOT NULL,
            meta_desc VARCHAR(320) DEFAULT '',
            show_in_footer TINYINT(1) NOT NULL DEFAULT 1,
            updated_at DATETIME NOT NULL
        ) $opt",
        "CREATE TABLE IF NOT EXISTS messages (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            email VARCHAR(150) NOT NULL,
            subject VARCHAR(200) DEFAULT '',
            message TEXT NOT NULL,
            ip VARCHAR(45) DEFAULT '',
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL
        ) $opt",
        "CREATE TABLE IF NOT EXISTS settings (
            k VARCHAR(100) NOT NULL PRIMARY KEY,
            v LONGTEXT
        ) $opt",
        "CREATE TABLE IF NOT EXISTS tools_status (
            slug VARCHAR(100) NOT NULL PRIMARY KEY,
            enabled TINYINT(1) NOT NULL DEFAULT 1,
            views INT UNSIGNED NOT NULL DEFAULT 0
        ) $opt",
        "CREATE TABLE IF NOT EXISTS custom_tools (
            slug VARCHAR(100) NOT NULL PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            icon VARCHAR(60) NOT NULL DEFAULT 'bi-tools',
            category VARCHAR(60) NOT NULL DEFAULT 'Utility Tools',
            description VARCHAR(300) NOT NULL DEFAULT '',
            about TEXT,
            how_to_use TEXT,
            faq TEXT,
            libs VARCHAR(600) NOT NULL DEFAULT '',
            code LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        ) $opt",
    );
}

function default_settings($siteName, $email)
{
    return array(
        'site_name' => $siteName,
        'tagline' => 'Free online tools & helpful guides',
        'meta_description' => $siteName . ' offers free online tools like Text to Image, Text to PDF, Image to PDF, PDF to Image, image compressor and more, plus useful blog articles. No signup, 100% private.',
        'contact_email' => $email,
        'site_url' => '',
        'adsense_client' => '',
        'ad_header' => '', 'ad_sidebar' => '', 'ad_in_article' => '', 'ad_tool_top' => '', 'ad_tool_bottom' => '', 'ad_footer' => '',
        'ads_txt' => '',
        'head_code' => '', 'footer_code' => '',
        'facebook' => '', 'twitter' => '', 'youtube' => '', 'instagram' => '', 'whatsapp' => '',
        'posts_per_page' => '9',
    );
}

function seed_pages()
{
    $about = '<h2>About {{site_name}}</h2>
<p>Welcome to <strong>{{site_name}}</strong> ({{site_url}}). We build simple, fast and completely free online tools that solve everyday problems: converting text and images to PDF, compressing pictures, extracting text from photos, generating QR codes and much more.</p>
<h3>Our mission</h3>
<p>Many websites ask you to sign up, upload your private files to unknown servers or pay for basic features. We believe useful tools should be free, quick and respectful of your privacy. That is why most of our tools work directly inside your browser: your files are processed on your own device and are never uploaded to our servers.</p>
<h3>What you will find here</h3>
<ul><li>PDF tools such as Text to PDF, Image to PDF, PDF to Image, Merge PDF and Split PDF</li>
<li>Image tools such as Text to Image, Image Compressor, Image Resizer, Format Converter and OCR</li>
<li>Text and utility tools such as Word Counter, Case Converter, Password Generator and QR Code Generator</li>
<li>A blog with step by step guides, tips and tutorials</li></ul>
<h3>Get in touch</h3>
<p>Have a suggestion for a new tool or found a problem? We would love to hear from you. Please visit our contact page or write to <a href="mailto:{{email}}">{{email}}</a>.</p>';

    $privacy = '<p><em>Last updated: {{year}}</em></p>
<p>At <strong>{{site_name}}</strong> ({{site_url}}) your privacy is important to us. This Privacy Policy explains what information we collect, how we use it and the choices you have.</p>
<h3>Information we collect</h3>
<p>We do not require you to create an account to use our tools. Files you select in our tools (images, PDFs, text) are processed locally in your web browser and are not uploaded to our servers. If you use our contact form we collect the name, e-mail address and message you submit so that we can reply to you.</p>
<h3>Log files</h3>
<p>Like most websites, our servers automatically record standard information such as IP address, browser type, pages visited and date and time. This information is used to analyze trends, administer the site and keep it secure.</p>
<h3>Cookies and Google AdSense</h3>
<p>We use cookies to store your preferences (for example dark mode) and to show advertising. Third party vendors, including Google, use cookies to serve ads based on your prior visits to this website or other websites. Google\'s use of advertising cookies enables it and its partners to serve ads to you based on your visit to our site and/or other sites on the Internet. You may opt out of personalized advertising by visiting <a href="https://www.google.com/settings/ads" target="_blank" rel="noopener">Google Ads Settings</a>. You can also opt out of a third-party vendor\'s use of cookies for personalized advertising by visiting <a href="https://www.aboutads.info" target="_blank" rel="noopener">www.aboutads.info</a>.</p>
<h3>Third party services</h3>
<p>Some tools load open source libraries from public content delivery networks (CDNs) such as cdnjs and jsDelivr. These providers may receive your IP address when the library is downloaded.</p>
<h3>Children\'s privacy</h3>
<p>Our website is not directed to children under 13 and we do not knowingly collect personal information from children.</p>
<h3>Your rights</h3>
<p>You may request access to, correction of, or deletion of any personal data we hold (such as contact form messages) by e-mailing us at <a href="mailto:{{email}}">{{email}}</a>.</p>
<h3>Changes to this policy</h3>
<p>We may update this policy from time to time. Any changes will be posted on this page with a new date.</p>';

    $terms = '<p><em>Last updated: {{year}}</em></p>
<p>By accessing and using <strong>{{site_name}}</strong> ({{site_url}}) you agree to the following terms and conditions.</p>
<h3>Use of the website</h3>
<p>Our tools and content are provided for lawful personal and commercial use. You agree not to misuse the website, attempt to disrupt its operation or use it to process content you do not have the right to use.</p>
<h3>Intellectual property</h3>
<p>The design, text, logos and code of this website are the property of {{site_name}} unless stated otherwise. You may not copy or republish our articles without written permission. Files you create with our tools belong to you.</p>
<h3>No warranty</h3>
<p>The tools are provided "as is" without warranties of any kind. We do not guarantee that results will be error free or that the service will always be available.</p>
<h3>Limitation of liability</h3>
<p>{{site_name}} will not be liable for any loss or damage arising from the use of this website or from reliance on the results produced by our tools.</p>
<h3>Changes</h3>
<p>We may modify these terms at any time. Continued use of the website means you accept the updated terms.</p>
<h3>Contact</h3>
<p>Questions about these terms? E-mail <a href="mailto:{{email}}">{{email}}</a>.</p>';

    $disc = '<p>The information and tools provided on <strong>{{site_name}}</strong> are for general informational and convenience purposes only.</p>
<h3>Accuracy</h3>
<p>We try to keep everything accurate and working correctly, but we make no guarantee about the completeness, reliability or accuracy of the tools and articles. Any action you take based on our content is strictly at your own risk.</p>
<h3>Advertising</h3>
<p>This website displays advertisements served by third parties such as Google AdSense. We are not responsible for the content of those advertisements or for the websites they link to.</p>
<h3>External links</h3>
<p>Our pages may contain links to external websites. We have no control over their content and accept no responsibility for it.</p>';

    return array(
        array('About Us', 'about-us', $about, 'Learn about our free online tools, our mission and how we protect your privacy.'),
        array('Privacy Policy', 'privacy-policy', $privacy, 'Read how we handle your data, cookies and advertising on our website.'),
        array('Terms & Conditions', 'terms-and-conditions', $terms, 'Terms and conditions for using our website and free online tools.'),
        array('Disclaimer', 'disclaimer', $disc, 'Website disclaimer regarding our tools, articles and advertising.'),
    );
}

function seed_posts()
{
    $p1 = '<p>Sending several scanned pages or photos separately is messy. Most offices, universities and online forms prefer a single PDF file. Fortunately you can convert images to PDF for free in less than a minute, without installing any software.</p>
<h2>Why convert images to PDF?</h2>
<ul><li><strong>One file instead of many:</strong> easier to email and upload.</li><li><strong>Consistent look:</strong> the pages open the same way on every phone and computer.</li><li><strong>Smaller and safer:</strong> a PDF is harder to edit accidentally than a folder of pictures.</li></ul>
<h2>Step by step</h2>
<ol><li>Open our <a href="/tools/image-to-pdf">Image to PDF tool</a>.</li><li>Drag your JPG, PNG or WEBP files into the upload box.</li><li>Arrange the images in the order you want the pages to appear.</li><li>Choose the page size (A4 is the standard for documents) and margins.</li><li>Press <em>Create PDF</em> and the file downloads instantly.</li></ol>
<h2>Tips for better results</h2>
<p>Take photos of documents in good light, keep the camera straight above the paper and crop away the background before converting. If the final PDF is too large to email, run the images through an <a href="/tools/image-compressor">image compressor</a> first: a quality of 75 to 80 percent usually makes files much smaller without visible loss.</p>
<h2>Is it private?</h2>
<p>Yes. The conversion happens inside your web browser. Your images are not uploaded to any server, which makes it safe for documents such as ID cards and certificates.</p>';

    $p2 = '<p>Large images slow down websites, fill up your phone storage and often exceed the size limits of job portals and government forms. Compressing an image reduces its file size, but done wrongly it can also make it blurry. Here is how to keep the quality.</p>
<h2>1. Choose the right format</h2>
<p>Use <strong>JPG</strong> for photographs, <strong>PNG</strong> for logos and screenshots with sharp edges and <strong>WEBP</strong> when you want the smallest possible file for the web.</p>
<h2>2. Do not save at 100 percent quality</h2>
<p>The difference between 100 and 80 percent quality is almost invisible, but the file can be three to five times smaller.</p>
<h2>3. Resize before you compress</h2>
<p>A phone photo is often 4000 pixels wide, while a blog only needs about 1200. Use an <a href="/tools/image-resizer">image resizer</a> and then apply compression.</p>
<h2>4. Compress in bulk</h2>
<p>Our <a href="/tools/image-compressor">Image Compressor</a> can process many pictures at once and shows how much space each one saved.</p>
<h2>Quick reference</h2>
<ul><li>Website photos: 1200 px wide, quality 75 to 80</li><li>Email attachments: 1600 px wide, quality 70</li><li>Profile pictures: 400 px wide, quality 85</li></ul>';

    $p3 = '<p>The internet is full of free tools, but many of them upload your files to remote servers. If you handle personal documents, that is a real privacy risk. Here are the essential tools everyone should know and how to use them safely.</p>
<h2>PDF tools</h2>
<p>Use <a href="/tools/merge-pdf">Merge PDF</a> to combine documents, <a href="/tools/split-pdf">Split PDF</a> to pull out selected pages and <a href="/tools/pdf-to-image">PDF to Image</a> when you need a page as a picture.</p>
<h2>Image tools</h2>
<p>The <a href="/tools/image-compressor">Image Compressor</a> and <a href="/tools/image-converter">Image Converter</a> save space and fix format problems. If you have a screenshot full of text, <a href="/tools/image-to-text">Image to Text</a> can extract it for you.</p>
<h2>Writing tools</h2>
<p>The <a href="/tools/word-counter">Word Counter</a> helps you meet assignment limits, and the <a href="/tools/case-converter">Case Converter</a> fixes text typed with caps lock on.</p>
<h2>Security tools</h2>
<p>Create unique passwords for every account with the <a href="/tools/password-generator">Password Generator</a>. Random passwords of 16 characters or more are extremely hard to guess.</p>
<h2>Why browser based tools are safer</h2>
<p>When a tool runs in your browser, your data stays on your device. Always prefer tools that clearly state that files are not uploaded.</p>';

    return array(
        array('How to Convert Images to PDF for Free (Step by Step)', 'how-to-convert-images-to-pdf-for-free', 'Learn how to combine JPG and PNG images into one PDF file online for free, without installing software.', 'Tutorials', $p1),
        array('5 Ways to Compress Images Without Losing Quality', 'compress-images-without-losing-quality', 'Simple tips to reduce image file size for websites, email and forms while keeping pictures sharp.', 'Tips & Tricks', $p2),
        array('Essential Free Online Tools Everyone Should Know', 'essential-free-online-tools', 'A short guide to the most useful free PDF, image, text and security tools and how to use them safely.', 'Guides', $p3),
    );
}
