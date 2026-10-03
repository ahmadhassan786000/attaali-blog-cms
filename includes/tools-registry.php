<?php
/*
 * TOOLS REGISTRY
 * ------------------------------------------------------------------
 * To add a NEW tool:
 *   1. Create the file  tool-ui/your-slug.php   (HTML + JavaScript of the tool)
 *   2. Add one entry below with the same slug.
 * That's all - the tool appears on the home page, tools page, menu and sitemap.
 * ------------------------------------------------------------------
 */

define('LIB_JSPDF', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
define('LIB_PDFJS', 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js');
define('LIB_PDFLIB', 'https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js');
define('LIB_JSZIP', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js');
define('LIB_QR', 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js');
define('LIB_TESSERACT', 'https://cdn.jsdelivr.net/npm/tesseract.js@5.1.1/dist/tesseract.min.js');

function tool_categories()
{
    return array(
        'PDF Tools' => array('icon' => 'bi-file-earmark-pdf', 'color' => 'danger', 'desc' => 'Create, merge, split and convert PDF files'),
        'Image Tools' => array('icon' => 'bi-image', 'color' => 'success', 'desc' => 'Compress, resize, convert and create images'),
        'Text Tools' => array('icon' => 'bi-fonts', 'color' => 'warning', 'desc' => 'Count, convert and generate text'),
        'Utility Tools' => array('icon' => 'bi-lightning-charge', 'color' => 'primary', 'desc' => 'Handy everyday helpers for everyone'),
    );
}

function tool_registry()
{
    static $t = null;
    if ($t !== null) {
        return $t;
    }
    $t = array();

    $t['text-to-image'] = array(
        'name' => 'AI Text to Image Generator', 'icon' => 'bi-stars', 'cat' => 'Image Tools',
        'desc' => 'Create an image from a written prompt and choose square, landscape, portrait or custom resolution.',
        'about' => 'AI Text to Image Generator turns your written idea into a visual image. Describe the subject, style, lighting and mood, choose the exact resolution you need and generate a downloadable result. Image creation is handled by an external provider, so your prompt is sent to that provider when you click Generate.',
        'how' => array('Describe the image you want in the prompt box.', 'Choose a preset resolution or enter a custom width and height.', 'Click Generate image and wait for the preview.', 'Download the result or open it in a new tab.'),
        'privacy' => 'This tool sends your prompt to an external image generation provider. Do not include private, confidential or sensitive information.',
        'faq' => array(
            array('Which resolutions can I use?', 'Choose a square, landscape, portrait or Full HD preset, or enter a custom width and height between 256 and 2048 pixels.'),
            array('How long does generation take?', 'Most images appear within a few seconds, but generation time depends on the provider and selected resolution.'),
            array('Is my prompt private?', 'The prompt is sent to the external image generation provider. Avoid entering passwords, personal data or confidential information.'),
        ),
        'libs' => array(),
    );

    $t['text-to-pdf'] = array(
        'name' => 'Text to PDF Converter', 'icon' => 'bi-file-earmark-text', 'cat' => 'PDF Tools',
        'desc' => 'Convert plain text into a neat, multi-page PDF document. Supports A4, Letter and Urdu/Unicode mode.',
        'about' => 'Text to PDF converts the text you type or paste into a properly formatted PDF file with page numbers, margins and your choice of font. It is perfect for notes, assignments, letters and articles. For normal English text the PDF keeps real, selectable text. If your text contains Urdu, Arabic or other non-Latin languages, switch on Unicode mode and the tool will render each page using your browser fonts so every letter appears correctly.',
        'how' => array('Paste or type your text into the editor.', 'Choose the page size, font, font size and margins.', 'Turn on Unicode mode if your text is in Urdu, Arabic or another non-Latin language.', 'Click Create PDF and your file downloads instantly.'),
        'faq' => array(
            array('How do I convert Urdu text to PDF?', 'Enable the Unicode / Urdu mode checkbox. The tool then renders every page with your browser fonts, which supports right-to-left languages properly.'),
            array('Is there a page limit?', 'No fixed limit. Very large documents may take a few seconds because everything is processed on your device.'),
            array('Can I select and copy text from the PDF?', 'In the standard mode yes. In Unicode mode the pages are images, so the text is not selectable.'),
        ),
        'libs' => array(LIB_JSPDF),
    );

    $t['image-to-pdf'] = array(
        'name' => 'Image to PDF Converter', 'icon' => 'bi-file-earmark-image', 'cat' => 'PDF Tools',
        'desc' => 'Combine JPG, PNG or WEBP images into one PDF file. Reorder pages and choose page size.',
        'about' => 'Image to PDF merges one or many pictures into a single PDF document. It is ideal for scanned documents, CNIC copies, receipts, assignments and photo albums. You can reorder the images, pick A4 or Letter size or fit the page exactly to each image, and set orientation and margins. The conversion happens locally in your browser, which keeps your private documents safe.',
        'how' => array('Click the upload box or drag and drop your images.', 'Use the arrow buttons to reorder pages or the trash icon to remove an image.', 'Select page size, orientation and margin.', 'Click Create PDF to download your document.'),
        'faq' => array(
            array('Which image formats are supported?', 'JPG, JPEG, PNG, WEBP, GIF and BMP images are supported.'),
            array('Are my images uploaded to a server?', 'No. Everything is processed in your browser, so your images never leave your device.'),
            array('Can I add many images at once?', 'Yes, select as many as you like. Each image becomes one page in the PDF.'),
        ),
        'libs' => array(LIB_JSPDF),
    );

    $t['pdf-to-image'] = array(
        'name' => 'PDF to Image Converter', 'icon' => 'bi-images', 'cat' => 'PDF Tools',
        'desc' => 'Convert every page of a PDF into high quality PNG or JPG images and download them as a ZIP.',
        'about' => 'PDF to Image renders each page of your PDF as a sharp picture. Choose PNG for the best quality or JPG for smaller files, select the resolution and download pages one by one or all together in a ZIP archive. It is useful for sharing a single page, creating thumbnails, or using PDF pages in presentations and social media posts.',
        'how' => array('Upload a PDF file.', 'Select the image format and quality scale.', 'Click Convert and wait while pages are rendered.', 'Download single pages or press Download all as ZIP.'),
        'faq' => array(
            array('What resolution should I choose?', '1.5x is good for screens. Choose 2x or 3x when you need print quality images.'),
            array('Can I convert password protected PDFs?', 'No. Please remove the password from the PDF first.'),
            array('Is it free and unlimited?', 'Yes, it is completely free and there is no daily limit.'),
        ),
        'libs' => array(LIB_PDFJS, LIB_JSZIP),
    );

    $t['merge-pdf'] = array(
        'name' => 'Merge PDF Files', 'icon' => 'bi-union', 'cat' => 'PDF Tools',
        'desc' => 'Join multiple PDF files into a single document in the order you want.',
        'about' => 'Merge PDF combines two or more PDF files into one. Add your files, drag them into the correct order using the arrows and download the combined document. All processing takes place inside your browser, so confidential contracts, bills and certificates stay private.',
        'how' => array('Add two or more PDF files.', 'Arrange them in the order you want using the up and down arrows.', 'Click Merge PDFs.', 'Your merged PDF downloads automatically.'),
        'faq' => array(
            array('Is there a limit on the number of files?', 'There is no limit set by us. Your device memory is the only limit.'),
            array('Will the quality change?', 'No. Pages are copied as they are, so the quality and text stay unchanged.'),
        ),
        'libs' => array(LIB_PDFLIB),
    );

    $t['split-pdf'] = array(
        'name' => 'Split PDF', 'icon' => 'bi-scissors', 'cat' => 'PDF Tools',
        'desc' => 'Extract selected pages from a PDF or split it into separate single-page files.',
        'about' => 'Split PDF helps you pull out only the pages you need. Enter a page range such as 1-3,5,8-10 to create a new PDF, or split the entire document into one file per page and download everything as a ZIP. Nothing is uploaded, the whole process happens on your computer.',
        'how' => array('Upload your PDF file.', 'Enter the pages to extract, for example 1-3,5,8-10, or choose the split every page option.', 'Click the button to process the file.', 'Download the new PDF or the ZIP archive.'),
        'faq' => array(
            array('How do I write the page range?', 'Separate pages or ranges with commas. Example: 1-3,7,10-12 extracts pages 1, 2, 3, 7, 10, 11 and 12.'),
            array('Does the original file change?', 'No. Your original file is untouched; you receive a new file.'),
        ),
        'libs' => array(LIB_PDFLIB, LIB_JSZIP),
    );

    $t['image-compressor'] = array(
        'name' => 'Image Compressor', 'icon' => 'bi-arrows-collapse', 'cat' => 'Image Tools',
        'desc' => 'Reduce the file size of JPG, PNG and WEBP images while keeping them sharp.',
        'about' => 'Image Compressor shrinks your pictures so websites load faster and files fit email or form upload limits. Adjust the quality slider, optionally limit the maximum width and instantly compare the original size with the compressed size. Multiple images can be processed at once and downloaded as a ZIP.',
        'how' => array('Upload one or more images.', 'Set the quality (70 to 85 percent usually looks identical to the original).', 'Optionally set a maximum width and output format.', 'Download each compressed image or all of them as a ZIP.'),
        'faq' => array(
            array('Which quality setting is best?', 'For photos, 75 to 85 percent gives a big reduction with almost no visible difference.'),
            array('Will PNG images become smaller?', 'PNG is lossless so savings are limited. Choose JPG or WEBP as the output format for much smaller files.'),
            array('Are my photos private?', 'Yes. Compression happens inside your browser and files are never uploaded.'),
        ),
        'libs' => array(LIB_JSZIP),
    );

    $t['image-resizer'] = array(
        'name' => 'Image Resizer', 'icon' => 'bi-aspect-ratio', 'cat' => 'Image Tools',
        'desc' => 'Resize images by pixels or percentage while keeping the aspect ratio.',
        'about' => 'Image Resizer changes the width and height of a picture. Enter exact pixel sizes or a percentage, lock the aspect ratio to avoid stretching and download the result in JPG, PNG or WEBP. It is handy for passport photos, social media posts, website banners and profile pictures.',
        'how' => array('Upload an image.', 'Enter the new width or height, or use the percentage option.', 'Keep Lock aspect ratio ticked to avoid distortion.', 'Click Resize and download your image.'),
        'faq' => array(
            array('Will resizing reduce quality?', 'Making an image smaller keeps it sharp. Enlarging beyond the original size can make it look soft.'),
            array('What is aspect ratio lock?', 'It keeps the width and height proportional so the picture is not stretched.'),
        ),
        'libs' => array(),
    );

    $t['image-converter'] = array(
        'name' => 'Image Format Converter', 'icon' => 'bi-arrow-left-right', 'cat' => 'Image Tools',
        'desc' => 'Convert images between JPG, PNG and WEBP formats in bulk.',
        'about' => 'Image Format Converter switches pictures between JPG, PNG and WEBP. Convert PNG screenshots to lightweight JPG, change photos to modern WEBP for your website or turn WEBP downloads into a format every app understands. Batch conversion and ZIP download are supported.',
        'how' => array('Upload one or more images.', 'Choose the target format: JPG, PNG or WEBP.', 'Set quality for JPG and WEBP.', 'Click Convert and download the results.'),
        'faq' => array(
            array('What happens to transparent backgrounds?', 'PNG and WEBP keep transparency. When converting to JPG the transparent area is filled with white.'),
            array('Is there a file size limit?', 'No limit from our side; very large images depend on your device memory.'),
        ),
        'libs' => array(LIB_JSZIP),
    );

    $t['image-to-text'] = array(
        'name' => 'Image to Text (OCR)', 'icon' => 'bi-body-text', 'cat' => 'Image Tools',
        'desc' => 'Extract text from photos and scanned documents using in-browser OCR (English, Urdu, Arabic, Hindi).',
        'about' => 'Image to Text uses optical character recognition, a form of machine learning, to read the words inside a picture and turn them into editable text. Upload a screenshot, a scanned page or a photo of a document, select the language and copy the extracted text. The recognition engine runs in your browser, so your images stay on your device. Clear, well lit images with straight text give the best accuracy.',
        'how' => array('Upload an image containing text.', 'Select the language of the text (English, Urdu, Arabic or Hindi).', 'Click Extract Text and wait for the progress bar to finish. The first run downloads the language data.', 'Copy the text or download it as a TXT file.'),
        'faq' => array(
            array('Why is the first extraction slow?', 'The language model has to be downloaded once. After that it is cached by your browser and is much faster.'),
            array('How can I improve accuracy?', 'Use a sharp, high contrast image, keep the text straight and select the correct language.'),
            array('Does it support handwriting?', 'OCR works best on printed text. Handwriting results are usually poor.'),
        ),
        'libs' => array(LIB_TESSERACT),
    );

    $t['qr-code-generator'] = array(
        'name' => 'QR Code Generator', 'icon' => 'bi-qr-code', 'cat' => 'Utility Tools',
        'desc' => 'Create free QR codes for links, text, phone numbers and Wi-Fi. Download as PNG.',
        'about' => 'QR Code Generator creates scannable codes for website links, plain text, phone numbers, e-mail addresses and Wi-Fi networks. Change the size and colors, then download the QR code as a PNG image for business cards, posters, menus and social media. The codes never expire and there are no scan limits.',
        'how' => array('Choose the QR type: Text/URL, Phone, Email or Wi-Fi.', 'Enter your content.', 'Pick size and colors.', 'Click Download PNG to save the QR code.'),
        'faq' => array(
            array('Do the QR codes expire?', 'No. The data is stored inside the code itself, so it works forever.'),
            array('Can I use them commercially?', 'Yes, the generated QR codes are free for personal and commercial use.'),
        ),
        'libs' => array(LIB_QR),
    );

    $t['word-counter'] = array(
        'name' => 'Word & Character Counter', 'icon' => 'bi-123', 'cat' => 'Text Tools',
        'desc' => 'Count words, characters, sentences, paragraphs and reading time instantly.',
        'about' => 'Word Counter gives you live statistics for any text: words, characters with and without spaces, sentences, paragraphs, estimated reading and speaking time and the most used keywords. Writers, students, bloggers and SEO professionals use it to meet word limits and to check keyword density.',
        'how' => array('Type or paste your text into the box.', 'Read the live statistics shown above the editor.', 'Check the top keywords list to avoid over-using a word.'),
        'faq' => array(
            array('How is reading time calculated?', 'We assume an average reading speed of 200 words per minute and speaking speed of 130 words per minute.'),
            array('Does it work for Urdu?', 'Yes, words are counted using spaces so it works for Urdu and other languages as well.'),
        ),
        'libs' => array(),
    );

    $t['case-converter'] = array(
        'name' => 'Case Converter', 'icon' => 'bi-type', 'cat' => 'Text Tools',
        'desc' => 'Change text to UPPERCASE, lowercase, Title Case, Sentence case, camelCase, snake_case and more.',
        'about' => 'Case Converter changes the capitalization of your text in one click. Fix accidental caps lock, prepare titles and headlines, or convert phrases into programming styles such as camelCase, snake_case and kebab-case. Copy the result or download it as a text file.',
        'how' => array('Paste your text into the box.', 'Click the case style you want.', 'Copy the converted text with the Copy button.'),
        'faq' => array(
            array('What is Title Case?', 'Title Case capitalizes the first letter of every word, commonly used for headings.'),
            array('What is the difference between camelCase and snake_case?', 'camelCase joins words and capitalizes each new word (myVariableName). snake_case joins words with underscores (my_variable_name).'),
        ),
        'libs' => array(),
    );

    $t['lorem-ipsum-generator'] = array(
        'name' => 'Lorem Ipsum Generator', 'icon' => 'bi-text-paragraph', 'cat' => 'Text Tools',
        'desc' => 'Generate placeholder text by paragraphs, sentences or words for your designs and websites.',
        'about' => 'Lorem Ipsum Generator produces dummy text for mockups, website templates and print layouts. Choose how many paragraphs, sentences or words you need, decide whether to start with the classic Lorem ipsum sentence and copy the result with one click.',
        'how' => array('Select paragraphs, sentences or words.', 'Enter how many you need.', 'Click Generate and copy the text.'),
        'faq' => array(
            array('What is Lorem Ipsum?', 'It is a placeholder text used in publishing and design to show how a layout looks before the real content is ready.'),
        ),
        'libs' => array(),
    );

    $t['password-generator'] = array(
        'name' => 'Strong Password Generator', 'icon' => 'bi-shield-lock', 'cat' => 'Utility Tools',
        'desc' => 'Generate secure random passwords with custom length and characters. Nothing is stored.',
        'about' => 'Password Generator creates strong, random passwords using your browser\'s cryptographic random number generator. Pick the length and the character types, then copy the password. Passwords are generated locally and are never sent or stored anywhere, so they remain completely private.',
        'how' => array('Move the slider to choose the password length (16+ is recommended).', 'Select uppercase, lowercase, numbers and symbols.', 'Click Generate and copy your new password.'),
        'faq' => array(
            array('How long should a password be?', 'At least 12 characters; 16 or more is best for important accounts.'),
            array('Do you save the passwords?', 'No. Passwords are created in your browser and disappear when you close the page.'),
        ),
        'libs' => array(),
    );

    $t['json-formatter'] = array(
        'name' => 'JSON Formatter & Validator', 'icon' => 'bi-braces', 'cat' => 'Utility Tools',
        'desc' => 'Beautify, minify and validate JSON data with clear error messages.',
        'about' => 'JSON Formatter makes messy JSON readable by adding indentation, or compresses it by removing all whitespace. The validator points out syntax errors so developers can quickly fix API responses, configuration files and data exports.',
        'how' => array('Paste your JSON into the input box.', 'Click Format to beautify, Minify to compress or Validate to check syntax.', 'Copy the output.'),
        'faq' => array(
            array('Is my data sent anywhere?', 'No. Formatting runs in your browser using JavaScript.'),
        ),
        'libs' => array(),
    );

    $t['base64-encoder'] = array(
        'name' => 'Base64 Encoder / Decoder', 'icon' => 'bi-code-slash', 'cat' => 'Utility Tools',
        'desc' => 'Encode text or files to Base64 and decode Base64 back to text. Supports Unicode.',
        'about' => 'Base64 Encoder and Decoder converts text or files into Base64 strings and back. It handles Unicode characters correctly and can turn an image into a data URI that you can embed directly in HTML or CSS.',
        'how' => array('Choose Encode or Decode.', 'Paste your text, or select a file to encode.', 'Copy the result.'),
        'faq' => array(
            array('What is Base64 used for?', 'Base64 represents binary data as text so it can be safely placed in e-mails, JSON, HTML and CSS.'),
        ),
        'libs' => array(),
    );

    $t['age-calculator'] = array(
        'name' => 'Age Calculator', 'icon' => 'bi-calendar-heart', 'cat' => 'Utility Tools',
        'desc' => 'Calculate exact age in years, months and days plus the countdown to your next birthday.',
        'about' => 'Age Calculator finds your exact age from your date of birth. It shows years, months and days, the total number of days, weeks and hours lived and how many days remain until your next birthday. You can also calculate age on any past or future date.',
        'how' => array('Select your date of birth.', 'Leave the second date as today, or pick another date.', 'Click Calculate to see your age.'),
        'faq' => array(
            array('Can I calculate age on a specific date?', 'Yes. Change the Age at the date field to any date you like.'),
        ),
        'libs' => array(),
    );

    return $t;
}

/* Custom tools added from the admin "Add New Tool" page, stored in the database */
function custom_tool_registry()
{
    $out = array();
    foreach (db_all('SELECT * FROM custom_tools ORDER BY created_at ASC') as $r) {
        $how = array();
        if ($r['how_to_use'] !== '') {
            foreach (preg_split('/\r?\n/', $r['how_to_use']) as $line) {
                $line = trim($line);
                if ($line !== '') { $how[] = $line; }
            }
        }
        $faq = array();
        if ($r['faq'] !== '') {
            $decoded = json_decode($r['faq'], true);
            if (is_array($decoded)) { $faq = $decoded; }
        }
        $libs = array();
        if ($r['libs'] !== '') {
            foreach (explode(',', $r['libs']) as $lib) {
                $lib = trim($lib);
                if ($lib !== '') { $libs[] = $lib; }
            }
        }
        $out[$r['slug']] = array(
            'name' => $r['name'], 'icon' => $r['icon'], 'cat' => $r['category'],
            'desc' => $r['description'], 'about' => $r['about'], 'how' => $how, 'faq' => $faq, 'libs' => $libs,
            'custom' => true,
        );
    }
    return $out;
}

/* Tools merged with database status (enabled / views) */
function all_tools($onlyEnabled = true)
{
    static $cache = null;
    if ($cache === null) {
        $cache = array();
        $status = array();
        foreach (db_all('SELECT slug, enabled, views FROM tools_status') as $r) {
            $status[$r['slug']] = $r;
        }
        foreach (tool_registry() as $slug => $tool) {
            if (!is_file(SITE_ROOT . '/tool-ui/' . $slug . '.php')) {
                continue;
            }
            $tool['slug'] = $slug;
            $tool['enabled'] = isset($status[$slug]) ? (int)$status[$slug]['enabled'] : 1;
            $tool['views'] = isset($status[$slug]) ? (int)$status[$slug]['views'] : 0;
            $cache[$slug] = $tool;
        }
        foreach (custom_tool_registry() as $slug => $tool) {
            if (!is_file(SITE_ROOT . '/tool-ui/' . $slug . '.php')) {
                continue;
            }
            $tool['slug'] = $slug;
            $tool['enabled'] = isset($status[$slug]) ? (int)$status[$slug]['enabled'] : 1;
            $tool['views'] = isset($status[$slug]) ? (int)$status[$slug]['views'] : 0;
            $cache[$slug] = $tool;
        }
    }
    if (!$onlyEnabled) {
        return $cache;
    }
    $out = array();
    foreach ($cache as $slug => $tool) {
        if ($tool['enabled']) {
            $out[$slug] = $tool;
        }
    }
    return $out;
}

function get_tool($slug)
{
    $all = all_tools(false);
    return isset($all[$slug]) ? $all[$slug] : null;
}

function tool_url($slug)
{
    return url('tools/' . $slug);
}

function tools_by_category($onlyEnabled = true)
{
    $out = array();
    foreach (tool_categories() as $cat => $meta) {
        $out[$cat] = array();
    }
    foreach (all_tools($onlyEnabled) as $slug => $tool) {
        $out[$tool['cat']][$slug] = $tool;
    }
    return $out;
}

