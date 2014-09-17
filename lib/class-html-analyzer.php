<?php
/*
HTML Analyzer 0.1
Shortens URLs by safely removing "http://example.com/" part from all urls.
*/

class HTML_Analyzer {
    // Variables
    protected $base_url = 'http://domain-to-remove.com';
    protected $html = '';
    protected $domd;

    public function __construct( $html ) {
        if ( $html !== '' ) {
            $this->base_url = home_url();
            $this->html     = $this->minifyHTML( $html );
        }
    }

    public function __toString() {
        return $this->html;
    }

    protected function minifyHTML( $html ) {
        $this->domd = new DOMDocument();
        libxml_use_internal_errors( true );
        $this->domd->loadHTML( $html );
        libxml_use_internal_errors( false );

        // <a href="http://">
        // <img src="http://" />
        // <script type="text/javascript" src="http://" />
        // <link rel="stylesheet" href="http://" />
        // <form method="get" action="http://">
        // <source type="video/mp4" src="http://" />

        $this->replaceUrls( "a", "href" );
        $this->replaceUrls( "img", "src" );
        $this->replaceUrls( "script", "src" );
        $this->replaceUrls( "link", "href" );
        $this->replaceUrls( "from", "action" );

        // IMPORTANT: ignore <source type="video/mp4" src="http://" /><a href="http://">http://</a></video>
        // $this->replaceUrls("source", "src");

        return $this->domd->saveHTML();
    }

    protected function replaceUrls( $tag, $attribute ) {
        $items = $this->domd->getElementsByTagName( $tag );

        foreach ( $items as $item ) {
            $url = $item->getAttribute( $attribute );
            if ( strpos( $url, $this->base_url ) === 0 ) {
                $url = substr( $url, strlen( $this->base_url ) );
                $item->setAttribute( $attribute, $url );
            }
        }
    }
}
?>