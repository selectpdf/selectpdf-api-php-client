<?php

/**
 * SelectPdf Online API .NET Client.
 */
namespace SelectPdf\Api {

	/**
     * Exception thrown by SelectPdf API Client.
	 */
	class ApiException extends \Exception {

		public function __toString() {
			if ($this->code) {
				return "({$this->code}) {$this->message}";
			}
			return "{$this->message}";
		}
	}

    /**
     * Base class for API clients. Do not use this directly.
     */
    class ApiClient
    {
        /**
         * API endpoint
         * @var mixed
         */
        protected $apiEndpoint = "https://selectpdf.com/api2/convert/";

        /**
         * Parameters that will be sent to the API.
         * @var mixed
         */
        protected $parameters = array();

        /**
         * HTTP Headers that will be sent to the API.
         * @var mixed
         */
        protected $headers = array();

        /**
         * Set a custom SelectPdf API endpoint. Do not use this method unless advised by SelectPdf.
         * @param mixed $apiEndpoint API endpoint.
         */
        public function setApiEndpoint($apiEndpoint)
        {
            $this->apiEndpoint = $apiEndpoint;
        }

        /**
         * Create a POST request.
         * @param mixed $outStream Output response to this stream, if specified.
         * @throws ApiException
         * @return string If output stream is not specified, return response as string.
         */
        protected function performPost($outStream)
        {
            // build headers string
            $allheaders = "Content-type: application/x-www-form-urlencoded\r\n";

            foreach($this->headers as $headerKey => $headerValue) {
                $allheaders = "$allheaders$headerKey: $headerValue\r\n";
            }

            // for options use key 'http' even if you send the request to https://...
            $options = array(
                'http' => array(
                    'header'  => $allheaders,
                    'method'  => 'POST',
                    'content' => http_build_query($this->parameters),
                    'ignore_errors' => true,
                    'timeout' => 600 // timeout in seconds 600s=10minutes
                ),
            );

            $context  = stream_context_create($options);
            $result = @file_get_contents($this->apiEndpoint, false, $context);

            $code = 0;
            $message = "";
            if (isset($http_response_header)) {
                $this->parseResponseHeaders($http_response_header, $code, $message);
            }

            if ($code === 0) {
                $error = error_get_last();
                $message = $error['message'];
            }

            // not ok - throw exception
            if ($code != 200) {
                if ($result) {
                    $message = $result;
                }
                throw new ApiException($message, $code);
            }

            // all ok - return pdf or write to stream
            if ($outStream == null)
                return $result;

            $written = fwrite($outStream, $result);
            if ($written != strlen($result)) {
                if (get_magic_quotes_runtime()) {
                    throw new ApiException("Error writing the PDF file to the specified path. This happens because the 'magic_quotes_runtime' setting is enabled. Please disable it either in php.ini file or in code by calling 'set_magic_quotes_runtime(false)'.");
                }
                throw new ApiException('Error writing the PDF file to the specified path.');
            }

        }

        /**
         * Serialize boolean values as "True" or "False" for the API.
         * @param mixed $value Value to be serialized.
         * @return string Serialized value.
         */
        public function serializeBoolean($value) {
            if (strtolower(strval($value)) == "false")
                $value = false;
            $serialized = $value ? "True" : "False";

            return $serialized;
        }

        private function parseResponseHeaders($headers, &$code, &$message) {
            //print_r($headers);
            if (!empty($headers)) {
                foreach ($headers as $header) {
                    if (preg_match('/HTTP\/\d\.\d\s+(\d+)\s*.*/', $header, $matches)) {
                        $code = intval($matches[1]);
                        $message = $matches[0];
                    }
                }
            }
        }

    }

    /**
     * Get usage details for SelectPdf Online API.
     */
    class UsageClient extends ApiClient {
        /**
         * Construct the Usage client.
         * @param mixed $apiKey API Key.
         */
        public function __construct($apiKey) {
            $this->apiEndpoint = "https://selectpdf.com/api2/usage/";
            $this->parameters["key"] = $apiKey;
        }

        /**
         * Get API usage information with history if specified.
         * @param mixed $getHistory Get history or not.
         * @return mixed Array containing usage information.
         */
        public function getUsage($getHistory = false) {
            $this->headers["Accept"] = "text/json";

            if ($getHistory)
            {
                $this->parameters["get_history"] = "True";
            }

            $result = $this->performPost(null);
            return json_decode($result, true);
       }
    }

    /**
     * PDF page size.
     */
    class PageSize {
        /**
         * Custom page size.
         */
        const Custom = "Custom";

        /**
         * A1 page size.
         */
        const A1 = "A1";

        /**
         * A2 page size.
         */
        const A2 = "A2";

        /**
         * A3 page size.
         */
        const A3 = "A3";

        /**
         * A4 page size.
         */
        const A4 = "A4";

        /**
         * A5 page size.
         */
        const A5 = "A5";

        /**
         * Letter page size.
         */
        const Letter = "Letter";

        /**
         * Half Letter page size.
         */
        const HalfLetter = "HalfLetter";

        /**
         * Ledger page size.
         */
        const Ledger = "Ledger";

        /**
         * Legal page size.
         */
        const Legal = "Legal";
    }

    /**
     * PDF page orientation.
     */
    class PageOrientation {
        /**
         * Portrait page orientation.
         */
        const Portrait = "Portrait";

        /**
         * Landscape page orientation.
         */
        const Landscape = "Landscape";
    }

    /**
     * Rendering engine used for HTML to PDF conversion.
     */
    class RenderingEngine {
        /**
         * WebKit rendering engine.
         */
        const WebKit = "WebKit";

        /**
         * WebKit Restricted rendering engine.
         */
        const Restricted = "Restricted";

        /**
         * Blink rendering engine.
         */
        const Blink = "Blink";

    }

    /**
     * Protocol used for secure (HTTPS) connections.
     */
    class SecureProtocol {
        /**
         * TLS 1.1 or newer. Recommended value.
         */
        const Tls11OrNewer = 0;

        /**
         * TLS 1.0 only.
         */
        const Tls10 = 1;

        /**
         * SSL v3 only.
         */
        const Ssl3 = 2;
    }

    /**
     * The page layout to be used when the pdf document is opened in a viewer.
     */
    class PageLayout {
        /**
         * Displays one page at a time.
         */
        const SinglePage = 0;

        /**
         * Displays the pages in one column.
         */
        const OneColumn = 1;

        /**
         * Displays the pages in two columns, with odd-numbered pages on the left.
         */
        const TwoColumnLeft = 2;

        /**
         * Displays the pages in two columns, with odd-numbered pages on the right.
         */
        const TwoColumnRight = 3;
    }

    /**
     * The PDF document's page mode.
     */
    class PageMode {
        /**
         * Neither document outline (bookmarks) nor thumbnail images are visible.
         */
        const UseNone = 0;

        /**
         * Document outline (bookmarks) are visible.
         */
        const UseOutlines = 1;

        /**
         * Thumbnail images are visible.
         */
        const UseThumbs = 2;

        /**
         * Full-screen mode, with no menu bar, window controls or any other window visible.
         */
        const FullScreen = 3;

        /**
         * Optional content group panel is visible.
         */
        const UseOC = 4;

        /**
         * Document attachments are visible.
         */
        const UseAttachments = 5;
    }

    /**
     * Alignment for page numbers.
     */
    class PageNumbersAlignment {
        /**
         * Align left.
         */
        const Left = 1;

        /**
         * Align center.
         */
        const Center = 2;

        /**
         * Align right.
         */
        const Right = 3;
    }

    /**
     * Specifies the converter startup mode.
     */
    class StartupMode {
        /**
         * The conversion starts right after the page loads.
         */
        const Automatic = "Automatic";

        /**
         * The conversion starts only when called from JavaScript.
         */
        const Manual = "Manual";
    }

    /**
     * Html To Pdf Conversion with SelectPdf Online API.
     */
    class HtmlToPdfClient extends ApiClient {
        /**
         * Construct the Html To Pdf Client.
         * @param mixed $apiKey API key.
         */
        public function __construct($apiKey)
        {
            $this->apiEndpoint = "https://selectpdf.com/api2/convert/";
            $this->parameters["key"] = $apiKey;
        }

        /**
         * Convert the specified url to PDF. SelectPdf online API can convert http:// and https:// publicly available urls.
         * @param mixed $url Address of the web page being converted.
         * @throws ApiException
         * @return string String containing the resulted PDF.
         */
        public function convertUrl($url)
        {
            if (strncasecmp($url, "http://", 7) != 0 && strncasecmp($url, "https://", 8) != 0) {
                throw new ApiException("The supported protocols for the converted webpage are http:// and https://.");
            }
            if (strncasecmp($url, "http://localhost", 16) === 0) {
                throw new ApiException("Cannot convert local urls. SelectPdf online API can only convert publicly available urls.");
            }
            $this->parameters["url"] = $url;

            return $this->performPost(null);
        }

        /**
         * Convert the specified url to PDF and writes the resulted PDF to an output stream. SelectPdf online API can convert http:// and https:// publicly available urls.
         * @param mixed $url Address of the web page being converted.
         * @param mixed $stream The output stream where the resulted PDF will be written.
         * @throws ApiException
         */
        public function convertUrlToStream($url, $stream)
        {
            if (strncasecmp($url, "http://", 7) != 0 && strncasecmp($url, "https://", 8) != 0) {
                throw new ApiException("The supported protocols for the converted webpage are http:// and https://.");
            }
            if (strncasecmp($url, "http://localhost", 16) === 0) {
                throw new ApiException("Cannot convert local urls. SelectPdf online API can only convert publicly available urls.");
            }
            $this->parameters["url"] = $url;

            $this->performPost(stream);
        }

        /**
         * Convert the specified url to PDF and writes the resulted PDF to a local file. SelectPdf online API can convert http:// and https:// publicly available urls.
         * @param mixed $url Address of the web page being converted.
         * @param mixed $filePath Local file including path if necessary.
         * @throws ApiException
         */
        public function convertUrlToFile($url, $filePath)
        {
            if (strncasecmp($url, "http://", 7) != 0 && strncasecmp($url, "https://", 8) != 0) {
                throw new ApiException("The supported protocols for the converted webpage are http:// and https://.");
            }
            if (strncasecmp($url, "http://localhost", 16) === 0) {
                throw new ApiException("Cannot convert local urls. SelectPdf online API can only convert publicly available urls.");
            }
            $this->parameters["url"] = $url;

            $outputFile = fopen($filePath, "wb");

            try
            {
                $this->performPost($outputFile);
                fclose($outputFile);
            }
            catch(ApiException $ex)
            {
                fclose($outputFile);
                unlink($filePath);
                throw $ex;
            }

        }

        /**
         * Convert the specified HTML string to PDF. Use a base url to resolve relative paths to resources.
         * @param mixed $htmlString HTML string with the content being converted.
         * @param mixed $baseUrl Base url used to resolve relative paths to resources (css, images, javascript, etc). Must be a http:// or https:// publicly available url.
         * @return string String containing the resulted PDF.
         */
        public function convertHtmlStringWithBaseUrl($htmlString, $baseUrl)
        {
            $this->parameters["html"] = $htmlString;

            if ($baseUrl != null && $baseUrl !== '')
            {
                $this->parameters["base_url"] = $baseUrl;
            }

            return $this->performPost(null);
        }

        /**
         * Convert the specified HTML string to PDF and writes the resulted PDF to an output stream. Use a base url to resolve relative paths to resources.
         * @param mixed $htmlString HTML string with the content being converted.
         * @param mixed $baseUrl Base url used to resolve relative paths to resources (css, images, javascript, etc). Must be a http:// or https:// publicly available url.
         * @param mixed $stream The output stream where the resulted PDF will be written.
         */
        public function convertHtmlStringToStreamWithBaseUrl($htmlString, $baseUrl, $stream)
        {
            $this->parameters["html"] = $htmlString;

            if ($baseUrl != null && $baseUrl !== '')
            {
                $this->parameters["base_url"] = $baseUrl;
            }

            $this->performPost($stream);
        }

        /**
         * Convert the specified HTML string to PDF and writes the resulted PDF to a local file. Use a base url to resolve relative paths to resources.
         * @param mixed $htmlString HTML string with the content being converted.
         * @param mixed $baseUrl Base url used to resolve relative paths to resources (css, images, javascript, etc). Must be a http:// or https:// publicly available url.
         * @param mixed $filePath Local file including path if necessary.
         * @throws ApiException
         */
        public function convertHtmlStringToFileWithBaseUrl($htmlString, $baseUrl, $filePath)
        {
            $this->parameters["html"] = $htmlString;

            if ($baseUrl != null && $baseUrl !== '')
            {
                $this->parameters["base_url"] = $baseUrl;
            }

            $outputFile = fopen($filePath, "wb");

            try
            {
                $this->performPost($outputFile);
                fclose($outputFile);
            }
            catch(ApiException $ex)
            {
                fclose($outputFile);
                unlink($filePath);
                throw $ex;
            }
        }

        /**
         * Convert the specified HTML string to PDF.
         * @param mixed $htmlString HTML string with the content being converted.
         * @return string String containing the resulted PDF.
         */
        public function convertHtmlString($htmlString)
        {
            return $this->convertHtmlStringWithBaseUrl($htmlString, null);
        }

        /**
         * Convert the specified HTML string to PDF and writes the resulted PDF to an output stream.
         * @param mixed $htmlString HTML string with the content being converted.
         * @param mixed $stream The output stream where the resulted PDF will be written.
         */
        public function convertHtmlStringToStream($htmlString, $stream)
        {
            $this->convertHtmlStringToStreamWithBaseUrl($htmlString, null, $stream);
        }

        /**
         * Convert the specified HTML string to PDF and writes the resulted PDF to a local file.
         * @param mixed $htmlString HTML string with the content being converted.
         * @param mixed $filePath Local file including path if necessary.
         */
        public function convertHtmlStringToFile($htmlString, $filePath)
        {
            $this->convertHtmlStringToFileWithBaseUrl($htmlString, null, $filePath);
        }

        /**
         * Set PDF page size. Default value is A4. If page size is set to Custom, use setPageWidth and setPageHeight methods to set the custom width/height of the PDF pages.
         * @param mixed $pageSize PDF page size. Possible values: Custom, A1, A2, A3, A4, A5, Letter, HalfLetter, Ledger, Legal. Use constants from \SelectPdf\Api\PageSize class.
         * @throws ApiException
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setPageSize($pageSize)
        {
            if (!preg_match("/(?i)^(Custom|A1|A2|A3|A4|A5|Letter|HalfLetter|Ledger|Legal)$/", $pageSize))
                throw new ApiException("Allowed values for Page Size: Custom, A1, A2, A3, A4, A5, Letter, HalfLetter, Ledger, Legal.");

            $this->parameters["page_size"] = $pageSize;
            return $this;
        }

        /**
         * Set PDF page width in points. Default value is 595pt (A4 page width in points). 1pt = 1/72 inch. This is taken into account only if page size is set to Custom using setPageSize method.
         * @param mixed $pageWidth Page width in points.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setPageWidth($pageWidth)
        {
            $this->parameters["page_width"] = $pageWidth;
            return $this;
        }

        /**
         * Set PDF page height in points. Default value is 842pt (A4 page height in points). 1pt = 1/72 inch. This is taken into account only if page size is set to Custom using setPageSize method.
         *
         * @param mixed $pageHeight Page height in points.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setPageHeight($pageHeight)
        {
            $this->parameters["page_height"] = $pageHeight;
            return $this;
        }

        /**
         * Set PDF page orientation. Default value is Portrait.
         * @param mixed $pageOrientation PDF page orientation. Possible values: Portrait, Landscape. Use constants from \SelectPdf\Api\PageOrientation class.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setPageOrientation($pageOrientation)
        {
            if (!preg_match("/(?i)^(Portrait|Landscape)$/", $pageOrientation))
                throw new ApiException("Allowed values for Page Orientation: Portrait, Landscape.");

            $this->parameters["page_orientation"] = $pageOrientation;
            return $this;
        }

        /**
         * Set top margin of the PDF pages. Default value is 5pt.
         * @param mixed $marginTop Margin value in points. 1pt = 1/72 inch.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setMarginTop($marginTop)
        {
            $this->parameters["margin_top"] = $marginTop;
            return $this;
        }

        /**
         * Set right margin of the PDF pages. Default value is 5pt.
         * @param mixed $marginRight Margin value in points. 1pt = 1/72 inch.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setMarginRight($marginRight)
        {
            $this->parameters["margin_right"] = $marginRight;
            return $this;
        }

        /**
         * Set bottom margin of the PDF pages. Default value is 5pt.
         * @param mixed $marginBottom Margin value in points. 1pt = 1/72 inch.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setMarginBottom($marginBottom)
        {
            $this->parameters["margin_bottom"] = $marginBottom;
            return $this;
        }

        /**
         * Set left margin of the PDF pages. Default value is 5pt.
         * @param mixed $marginLeft Margin value in points. 1pt = 1/72 inch.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setMarginLeft($marginLeft)
        {
            $this->parameters["margin_left"] = $marginLeft;
            return $this;
        }

        /**
         * Set all margins of the PDF pages to the same value. Default value is 5pt.
         * @param mixed $margin Margin value in points. 1pt = 1/72 inch.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setMargins($margin)
        {
            return $this->setMarginTop($margin)->setMarginRight($margin)->setMarginBottom($margin)->setMarginLeft($margin);
        }

        /**
         * Specify the name of the pdf document that will be created. The default value is Document.pdf.
         * @param mixed $pdfName Name of the generated PDF document.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setPdfName($pdfName)
        {
            $this->parameters["pdf_name"] = $pdfName;
            return $this;
        }

        /**
         * Set the rendering engine used for the HTML to PDF conversion. Default value is WebKit.
         * @param mixed $renderingEngine HTML rendering engine. Use constants from \SelectPdf\Api\RenderingEngine class.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setRenderingEngine($renderingEngine)
        {
            if (!preg_match("/(?i)^(WebKit|Restricted|Blink)$/", $renderingEngine))
                throw new ApiException("Allowed values for Rendering Engine: WebKit, Restricted, Blink.");

            $this->parameters["engine"] = $renderingEngine;
            return $this;
        }

        /**
         * Set PDF user password.
         * @param mixed $userPassword PDF user password.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setUserPassword($userPassword)
        {
            $this->parameters["user_password"] = $userPassword;
            return $this;
        }

        /**
         * Set PDF owner password.
         * @param mixed $ownerPassword PDF owner password.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setOwnerPassword($ownerPassword)
        {
            $this->parameters["owner_password"] = $ownerPassword;
            return $this;
        }

        /**
         * Set the width used by the converter's internal browser window in pixels. The default value is 1024px.
         * @param mixed $webPageWidth Browser window width in pixels.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setWebPageWidth($webPageWidth)
        {
            $this->parameters["web_page_width"] = $webPageWidth;
            return $this;
        }

        /**
         * Set the height used by the converter's internal browser window in pixels. The default value is 0px and it means that the page height is automatically calculated by the converter.
         * @param mixed $webPageHeight Browser window height in pixels. Set it to 0px to automatically calculate page height.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setWebPageHeight($webPageHeight)
        {
            $this->parameters["web_page_height"] = $webPageHeight;
            return $this;
        }

        /**
         * Introduce a delay (in seconds) before the actual conversion to allow the web page to fully load. This method is an alias for setConversionDelay. The default value is 1 second. Use a larger value if the web page has content that takes time to render when it is displayed in the browser.
         * @param mixed $minLoadTime Delay in seconds.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setMinLoadTime($minLoadTime)
        {
            $this->parameters["min_load_time"] = $minLoadTime;
            return $this;
        }

        /**
         * Introduce a delay (in seconds) before the actual conversion to allow the web page to fully load. This method is an alias for setMinLoadTime. The default value is 1 second. Use a larger value if the web page has content that takes time to render when it is displayed in the browser.
         * @param mixed $delay Delay in seconds.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setConversionDelay($delay)
        {
            return $this->setMinLoadTime($delay);
        }

        /**
         * Set the maximum amount of time (in seconds) that the convert will wait for the page to load. This method is an alias for setNavigationTimeout. A timeout error is displayed when this time elapses. The default value is 30 seconds. Use a larger value (up to 120 seconds allowed) for pages that take a long time to load.
         * @param mixed $maxLoadTime Timeout in seconds.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setMaxLoadTime($maxLoadTime)
        {
            $this->parameters["max_load_time"] = $maxLoadTime;
            return $this;
        }

        /**
         * Set the maximum amount of time (in seconds) that the convert will wait for the page to load. This method is an alias for setMaxLoadTime. A timeout error is displayed when this time elapses. The default value is 30 seconds. Use a larger value (up to 120 seconds allowed) for pages that take a long time to load.
         * @param mixed $timeout Timeout in seconds.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setNavigationTimeout($timeout)
        {
            return $this->setMaxLoadTime($timeout);
        }

        /**
         * Set the protocol used for secure (HTTPS) connections. Set this only if you have an older server that only works with older SSL connections.
         * @param mixed $secureProtocol Secure protocol. Possible values: 0 (TLS 1.1 or newer), 1 (TLS 1.0), 2 (SSL v3 only). Use constants from \SelectPdf\Api\SecureProtocol class.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setSecureProtocol($secureProtocol)
        {
            if ($secureProtocol != 0 && $secureProtocol != 1 && $secureProtocol != 2)
                throw new ApiException("Allowed values for Secure Protocol: 0 (TLS 1.1 or newer), 1 (TLS 1.0), 2 (SSL v3 only).");

            $this->parameters["protocol"] = $secureProtocol;
            return $this;
        }

        /**
         * Specify if the CSS Print media type is used instead of the Screen media type. The default value is False.
         * @param mixed $useCssPrint Use CSS Print media or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setUseCssPrint($useCssPrint)
        {
            $this->parameters["use_css_print"] = $this->serializeBoolean($useCssPrint);
            return $this;
        }

        /**
         * Specify the background color of the PDF page in RGB html format. The default is #FFFFFF.
         * @param mixed $backgroundColor Background color in #RRGGBB format.
         * @throws ApiException
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setBackgroundColor($backgroundColor)
        {
            if (!preg_match("/^#?[0-9a-fA-F]{6}$/", $backgroundColor))
                throw new ApiException("Color value must be in #RRGGBB format.");

            $this->parameters["background_color"] = $backgroundColor;
            return $this;
        }

        /**
         * Set a flag indicating if the web page background is rendered in PDF. The default value is True.
         * @param mixed $drawHtmlBackground Draw the HTML background or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setDrawHtmlBackground($drawHtmlBackground)
        {
            $this->parameters["draw_html_background"] = $this->serializeBoolean($drawHtmlBackground);
            return $this;
        }

        /**
         * Do not run JavaScript in web pages. The default value is False and javascript is executed.
         * @param mixed $disableJavascript Disable javascript or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setDisableJavascript($disableJavascript)
        {
            $this->parameters["disable_javascript"] = $this->serializeBoolean($disableJavascript);
            return $this;
        }

        /**
         * Do not create internal links in the PDF. The default value is False and internal links are created.
         * @param mixed $disableInternalLinks Disable internal links or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setDisableInternalLinks($disableInternalLinks)
        {
            $this->parameters["disable_internal_links"] = $this->serializeBoolean($disableInternalLinks);
            return $this;
        }

        /**
         * Do not create external links in the PDF. The default value is False and external links are created.
         * @param mixed $disableExternalLinks Disable external links or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setDisableExternalLinks($disableExternalLinks)
        {
            $this->parameters["disable_external_links"] = $this->serializeBoolean($disableExternalLinks);
            return $this;
        }

        /**
         * Try to render the PDF even in case of the web page loading timeout. The default value is False and an exception is raised in case of web page navigation timeout.
         * @param mixed $renderOnTimeout Render in case of timeout or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setRenderOnTimeout($renderOnTimeout)
        {
            $this->parameters["render_on_timeout"] = $this->serializeBoolean($renderOnTimeout);
            return $this;
        }

        /**
         * Avoid breaking images between PDF pages. The default value is False and images are split between pages if larger.
         * @param mixed $keepImagesTogether Try to keep images on same page or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setKeepImagesTogether($keepImagesTogether)
        {
            $this->parameters["keep_images_together"] = $this->serializeBoolean($keepImagesTogether);
            return $this;
        }

        /**
         * Set the PDF document title.
         * @param mixed $docTitle Document title.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setDocTitle($docTitle)
        {
            $this->parameters["doc_title"] = $docTitle;
            return $this;
        }

        /**
         * Set the subject of the PDF document.
         * @param mixed $docSubject Document subject.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setDocSubject($docSubject)
        {
            $this->parameters["doc_subject"] = $docSubject;
            return $this;
        }

        /**
         * Set the PDF document keywords.
         * @param mixed $docKeywords Document keywords.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setDocKeywords($docKeywords)
        {
            $this->parameters["doc_keywords"] = $docKeywords;
            return $this;
        }

        /**
         * Set the name of the PDF document author.
         * @param mixed $docAuthor Document author.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setDocAuthor($docAuthor)
        {
            $this->parameters["doc_author"] = $docAuthor;
            return $this;
        }

        /**
         * Add the date and time when the PDF document was created to the PDF document information. The default value is False.
         * @param mixed $docAddCreationDate Add creation date to the document metadata or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setDocAddCreationDate($docAddCreationDate)
        {
            $this->parameters["doc_add_creation_date"] = $this->serializeBoolean($docAddCreationDate);
            return $this;
        }

        /**
         * Set the page layout to be used when the document is opened in a PDF viewer. The default value is 1 - OneColumn.
         * @param mixed $pageLayout Page layout. Possible values: 0 (Single Page), 1 (One Column), 2 (Two Column Left), 3 (Two Column Right). Use constants from \SelectPdf\Api\PageLayout class.
         * @throws ApiException
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setViewerPageLayout($pageLayout)
        {
            if ($pageLayout != 0 && $pageLayout != 1 && $pageLayout != 2 && $pageLayout != 3)
                throw new ApiException("Allowed values for Page Layout: 0 (Single Page), 1 (One Column), 2 (Two Column Left), 3 (Two Column Right).");

            $this->parameters["viewer_page_layout"] = $pageLayout;
            return $this;
        }

        /**
         * Set the document page mode when the pdf document is opened in a PDF viewer. The default value is 0 - UseNone.
         * @param mixed $pageMode Page mode. Possible values: 0 (Use None), 1 (Use Outlines), 2 (Use Thumbs), 3 (Full Screen), 4 (Use OC), 5 (Use Attachments). Use constants from \SelectPdf\Api\PageMode class.
         * @throws ApiException
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setViewerPageMode($pageMode)
        {
            if ($pageMode != 0 && $pageMode != 1 && $pageMode != 2 && $pageMode != 3 && $pageMode != 4 && $pageMode != 5)
                throw new ApiException("Allowed values for Page Mode: 0 (Use None), 1 (Use Outlines), 2 (Use Thumbs), 3 (Full Screen), 4 (Use OC), 5 (Use Attachments).");

            $this->parameters["viewer_page_mode"] = $pageMode;
            return $this;
        }

        /**
         * Set a flag specifying whether to position the document's window in the center of the screen. The default value is False.
         * @param mixed $viewerCenterWindow Center window or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setViewerCenterWindow($viewerCenterWindow)
        {
            $this->parameters["viewer_center_window"] = $this->serializeBoolean($viewerCenterWindow);
            return $this;
        }

        /**
         * Set a flag specifying whether the window's title bar should display the document title taken from document information. The default value is False.
         * @param mixed $viewerDisplayDocTitle Display title or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setViewerDisplayDocTitle($viewerDisplayDocTitle)
        {
            $this->parameters["viewer_display_doc_title"] = $this->serializeBoolean($viewerDisplayDocTitle);
            return $this;
        }

        /**
         * Set a flag specifying whether to resize the document's window to fit the size of the first displayed page. The default value is False.
         * @param mixed $viewerFitWindow Fit window or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setViewerFitWindow($viewerFitWindow)
        {
            $this->parameters["viewer_fit_window"] = $this->serializeBoolean($viewerFitWindow);
            return $this;
        }

        /**
         * Set a flag specifying whether to hide the pdf viewer application's menu bar when the document is active. The default value is False.
         * @param mixed $viewerHideMenuBar Hide menu bar or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setViewerHideMenuBar($viewerHideMenuBar)
        {
            $this->parameters["viewer_hide_menu_bar"] = $this->serializeBoolean($viewerHideMenuBar);
            return $this;
        }

        /**
         * Set a flag specifying whether to hide the pdf viewer application's tool bars when the document is active. The default value is False.
         * @param mixed $viewerHideToolbar Hide tool bars or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setViewerHideToolbar($viewerHideToolbar)
        {
            $this->parameters["viewer_hide_toolbar"] = $this->serializeBoolean($viewerHideToolbar);
            return $this;
        }

        /**
         * Set a flag specifying whether to hide user interface elements in the document's window (such as scroll bars and navigation controls), leaving only the document's contents displayed.
         * @param mixed $viewerHideWindowUI Hide window UI or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setViewerHideWindowUI($viewerHideWindowUI)
        {
            $this->parameters["viewer_hide_window_ui"] = $this->serializeBoolean($viewerHideWindowUI);
            return $this;
        }

        /**
         * Control if a custom header is displayed in the generated PDF document. The default value is False.
         * @param mixed $showHeader Show header or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setShowHeader($showHeader)
        {
            $this->parameters["show_header"] = $this->serializeBoolean($showHeader);
            return $this;
        }

        /**
         * The height of the pdf document header. This height is specified in points. 1 point is 1/72 inch. The default value is 50.
         * @param mixed $height Header height.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setHeaderHeight($height)
        {
            $this->parameters["header_height"] = $height;
            return $this;
        }

        /**
         * Set the url of the web page that is converted and rendered in the PDF document header.
         * @param mixed $url The url of the web page that is converted and rendered in the pdf document header.
         * @throws ApiException
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setHeaderUrl($url)
        {
            if (strncasecmp($url, "http://", 7) != 0 && strncasecmp($url, "https://", 8) != 0) {
                throw new ApiException("The supported protocols for the url are http:// and https://.");
            }
            if (strncasecmp($url, "http://localhost", 16) === 0) {
                throw new ApiException("Cannot convert local urls. SelectPdf online API can only convert publicly available urls.");
            }

            $this->parameters["header_url"] = $url;
            return $this;
        }

        /**
         * Set the raw html that is converted and rendered in the pdf document header.
         * @param mixed $html The raw html that is converted and rendered in the pdf document header.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setHeaderHtml($html)
        {
            $this->parameters["header_html"] = $html;
            return $this;
        }

        /**
         * Set an optional base url parameter can be used together with the header HTML to resolve relative paths from the html string.
         * @param mixed $baseUrl Header base url.
         * @throws ApiException
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setHeaderBaseUrl($baseUrl)
        {
            if (strncasecmp($baseUrl, "http://", 7) != 0 && strncasecmp($baseUrl, "https://", 8) != 0) {
                throw new ApiException("The supported protocols for the base url are http:// and https://.");
            }
            if (strncasecmp($baseUrl, "http://localhost", 16) === 0) {
                throw new ApiException("Cannot convert local urls. SelectPdf online API can only convert publicly available urls.");
            }

            $this->parameters["header_base_url"] = $baseUrl;
            return $this;
        }

        /**
         * Control the visibility of the header on the first page of the generated pdf document. The default value is True.
         * @param mixed $displayOnFirstPage Display header on the first page or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setHeaderDisplayOnFirstPage($displayOnFirstPage)
        {
            $this->parameters["header_display_on_first_page"] = $this->serializeBoolean($displayOnFirstPage);
            return $this;
        }

        /**
         * Control the visibility of the header on the odd numbered pages of the generated pdf document. The default value is True.
         * @param mixed $displayOnOddPages Display header on odd pages or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setHeaderDisplayOnOddPages($displayOnOddPages)
        {
            $this->parameters["header_display_on_odd_pages"] = $this->serializeBoolean($displayOnOddPages);
            return $this;
        }

        /**
         * Control the visibility of the header on the even numbered pages of the generated pdf document. The default value is True.
         * @param mixed $displayOnEvenPages Display header on even pages or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setHeaderDisplayOnEvenPages($displayOnEvenPages)
        {
            $this->parameters["header_display_on_even_pages"] = $this->serializeBoolean($displayOnEvenPages);
            return $this;
        }


        /**
         * Control if a custom footer is displayed in the generated PDF document. The default value is False.
         * @param mixed $showFooter Show footer or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setShowFooter($showFooter)
        {
            $this->parameters["show_footer"] = $this->serializeBoolean($showFooter);
            return $this;
        }

        /**
         * The height of the pdf document footer. This height is specified in points. 1 point is 1/72 inch. The default value is 50.
         * @param mixed $height Footer height.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setFooterHeight($height)
        {
            $this->parameters["footer_height"] = $height;
            return $this;
        }

        /**
         * Set the url of the web page that is converted and rendered in the PDF document footer.
         * @param mixed $url The url of the web page that is converted and rendered in the pdf document footer.
         * @throws ApiException
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setFooterUrl($url)
        {
            if (strncasecmp($url, "http://", 7) != 0 && strncasecmp($url, "https://", 8) != 0) {
                throw new ApiException("The supported protocols for the url are http:// and https://.");
            }
            if (strncasecmp($url, "http://localhost", 16) === 0) {
                throw new ApiException("Cannot convert local urls. SelectPdf online API can only convert publicly available urls.");
            }

            $this->parameters["footer_url"] = $url;
            return $this;
        }

        /**
         * Set the raw html that is converted and rendered in the pdf document footer.
         * @param mixed $html The raw html that is converted and rendered in the pdf document footer.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setFooterHtml($html)
        {
            $this->parameters["footer_html"] = $html;
            return $this;
        }

        /**
         * Set an optional base url parameter can be used together with the footer HTML to resolve relative paths from the html string.
         * @param mixed $baseUrl Footer base url.
         * @throws ApiException
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setFooterBaseUrl($baseUrl)
        {
            if (strncasecmp($baseUrl, "http://", 7) != 0 && strncasecmp($baseUrl, "https://", 8) != 0) {
                throw new ApiException("The supported protocols for the base url are http:// and https://.");
            }
            if (strncasecmp($baseUrl, "http://localhost", 16) === 0) {
                throw new ApiException("Cannot convert local urls. SelectPdf online API can only convert publicly available urls.");
            }

            $this->parameters["footer_base_url"] = $baseUrl;
            return $this;
        }

        /**
         * Control the visibility of the footer on the first page of the generated pdf document. The default value is True.
         * @param mixed $displayOnFirstPage Display footer on the first page or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setFooterDisplayOnFirstPage($displayOnFirstPage)
        {
            $this->parameters["footer_display_on_first_page"] = $this->serializeBoolean($displayOnFirstPage);
            return $this;
        }

        /**
         * Control the visibility of the footer on the odd numbered pages of the generated pdf document. The default value is True.
         * @param mixed $displayOnOddPages Display footer on odd pages or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setFooterDisplayOnOddPages($displayOnOddPages)
        {
            $this->parameters["footer_display_on_odd_pages"] = $this->serializeBoolean($displayOnOddPages);
            return $this;
        }

        /**
         * Control the visibility of the footer on the even numbered pages of the generated pdf document. The default value is True.
         * @param mixed $displayOnEvenPages Display footer on even pages or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setFooterDisplayOnEvenPages($displayOnEvenPages)
        {
            $this->parameters["footer_display_on_even_pages"] = $this->serializeBoolean($displayOnEvenPages);
            return $this;
        }

        /**
         * Add a special footer on the last page of the generated pdf document only. The default value is False. Use setFooterUrl or setFooterHtml and setFooterBaseUrl to specify the content of the last page footer. Use setFooterHeight to specify the height of the special last page footer.
         * @param mixed $displayOnLastPage Display special footer on the last page or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setFooterDisplayOnLastPage($displayOnLastPage)
        {
            $this->parameters["footer_display_on_last_page"] = $this->serializeBoolean($displayOnLastPage);
            return $this;
        }


        /**
         * Show page numbers. Default value is True. Page numbers will be displayed in the footer of the PDF document.
         * @param mixed $showPageNumbers Show page numbers or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setShowPageNumbers($showPageNumbers)
        {
            $this->parameters["page_numbers"] = $this->serializeBoolean($showPageNumbers);
            return $this;
        }

        /**
         * Control the page number for the first page being rendered. The default value is 1.
         * @param mixed $firstPageNumber First page number.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setPageNumbersFirst($firstPageNumber)
        {
            $this->parameters["page_numbers_first"] = $firstPageNumber;
            return $this;
        }

        /**
         * Control the total number of pages offset in the generated pdf document. The default value is 0.
         * @param mixed $totalPagesOffset Offset for the total number of pages in the generated pdf document.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setPageNumbersOffset($totalPagesOffset)
        {
            $this->parameters["page_numbers_offset"] = $totalPagesOffset;
            return $this;
        }

        /**
         * Set the text that is used to display the page numbers. It can contain the placeholder {page_number} for the current page number and {total_pages} for the total number of pages. The default value is "Page: {page_number} of {total_pages}".
         * @param mixed $template Page numbers template.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setPageNumbersTemplate($template)
        {
            $this->parameters["page_numbers_template"] = $template;
            return $this;
        }

        /**
         * Set the font used to display the page numbers text. The default value is "Helvetica".
         * @param mixed $fontName The font used to display the page numbers text.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setPageNumbersFontName($fontName)
        {
            $this->parameters["page_numbers_font_name"] = $fontName;
            return $this;
        }

        /**
         * Set the size of the font used to display the page numbers. The default value is 10 points.
         * @param mixed $fontSize The size in points of the font used to display the page numbers.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setPageNumbersFontSize($fontSize)
        {
            $this->parameters["page_numbers_font_size"] = $fontSize;
            return $this;
        }

        /**
         * Set the alignment of the page numbers text. The default value is "2" - PageNumbersAlignment.Right.
         * @param mixed $alignment The alignment of the page numbers text. Possible values: 1 (Left), 2 (Center), 3 (Right). Use constants from \SelectPdf\Api\PageNumbersAlignment class.
         * @throws ApiException
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setPageNumbersAlignment($alignment)
        {
            if ($alignment != 1 && $alignment != 2 && $alignment != 3)
                throw new ApiException("Allowed values for Page Numbers Alignment: 1 (Left), 2 (Center), 3 (Right).");

            $this->parameters["page_numbers_alignment"] = $alignment;
            return $this;
        }

        /**
         * Specify the color of the page numbers text in #RRGGBB html format. The default value is #333333.
         * @param mixed $color Page numbers color.
         * @throws ApiException
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setPageNumbersColor($color)
        {
            if (!preg_match("/^#?[0-9a-fA-F]{6}$/", $color))
                throw new ApiException("Color value must be in #RRGGBB format.");

            $this->parameters["page_numbers_color"] = $color;
            return $this;
        }

        /**
         * Specify the position in points on the vertical where the page numbers text is displayed in the footer. The default value is 10 points.
         * @param mixed $position Page numbers Y position in points.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setPageNumbersVerticalPosition($position)
        {
            $this->parameters["page_numbers_pos_y"] = $position;
            return $this;
        }

        /**
         * Generate automatic bookmarks in pdf. The elements that will be bookmarked are defined using CSS selectors. For example, the selector for all the H1 elements is "H1", the selector for all the elements with the CSS class name 'myclass' is "*.myclass" and the selector for the elements with the id 'myid' is "*#myid". Read more about CSS selectors <a href="http://www.w3schools.com/cssref/css_selectors.asp" target="_blank">here</a>.
         * @param mixed $selectors CSS selectors used to identify HTML elements.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setPdfBookmarksSelectors($selectors)
        {
            $this->parameters["pdf_bookmarks_selectors"] = $selectors;
            return $this;
        }

        /**
         * Exclude page elements from the conversion. The elements that will be excluded are defined using CSS selectors. For example, the selector for all the H1 elements is "H1", the selector for all the elements with the CSS class name 'myclass' is "*.myclass" and the selector for the elements with the id 'myid' is "*#myid". Read more about CSS selectors <a href="http://www.w3schools.com/cssref/css_selectors.asp" target="_blank">here</a>.
         * @param mixed $selectors CSS selectors used to identify HTML elements.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setPdfHideElements($selectors)
        {
            $this->parameters["pdf_hide_elements"] = $selectors;
            return $this;
        }

        /**
         * Convert only a specific section of the web page to pdf. The section that will be converted to pdf is specified by the html element ID. The element can be anything (image, table, table row, div, text, etc).
         * @param mixed $elementID HTML element ID.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setPdfShowOnlyElementID($elementID)
        {
            $this->parameters["pdf_show_only_element_id"] = $elementID;
            return $this;
        }

        /**
         * Set converter startup mode. The default value is StartupMode.Automatic and the conversion is started immediately. By default this is set to StartupMode.Automatic and the conversion is started as soon as the page loads (and conversion delay set with setConversionDelay elapses). If set to StartupMode.Manual, the conversion is started only by a javascript call to SelectPdf.startConversion() from within the web page.
         * @param mixed $startupMode Converter startup mode. Possible values: Automatic, Manual. Use constants from \SelectPdf\Api\StartupMode class.
         * @throws ApiException
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setStartupMode($startupMode)
        {
            if (!preg_match("/(?i)^(Automatic|Manual)$/", $startupMode))
                throw new ApiException("Allowed values for Startup Mode: Automatic, Manual.");

            $this->parameters["startup_mode"] = $startupMode;
            return $this;
        }

        /**
         * Internal use only.
         * @param mixed $skipDecoding The default value is True.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setSkipDecoding($skipDecoding)
        {
            $this->parameters["skip_decoding"] = $this->serializeBoolean($skipDecoding);
            return $this;
        }

        /**
         * Set a flag indicating if the images from the page are scaled during the conversion process. The default value is False and images are not scaled.
         * @param mixed $scaleImages Scale images or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setScaleImages($scaleImages)
        {
            $this->parameters["scale_images"] = $this->serializeBoolean($scaleImages);
            return $this;
        }

        /**
         * Generate a single page PDF. The converter will automatically resize the PDF page to fit all the content in a single page. The default value of this property is False and the PDF will contain several pages if the content is large.
         * @param mixed $generateSinglePagePdf Generate a single page PDF or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setSinglePagePdf($generateSinglePagePdf)
        {
            $this->parameters["single_page_pdf"] = $this->serializeBoolean($generateSinglePagePdf);
            return $this;
        }

        /**
         * Get or set a flag indicating if an enhanced custom page breaks algorithm is used. The enhanced algorithm is a little bit slower but it will prevent the appearance of hidden text in the PDF when custom page breaks are used. The default value for this property is False.
         * @param mixed $enableEnhancedPageBreaksAlgorithm Enable enhanced page breaks algorithm or not.
         * @return HtmlToPdfClient Reference to the current object.
         */
        public function setPageBreaksEnhancedAlgorithm($enableEnhancedPageBreaksAlgorithm)
        {
            $this->parameters["page_breaks_enhanced_algorithm"] = $this->serializeBoolean($enableEnhancedPageBreaksAlgorithm);
            return $this;
        }

    }
}
?>
