<?php
/*
Plugin Name: Reklamshop Editor
Plugin URI: https://reklamshop.se/
Description:  This plugin design editor for Reklamshop
Version: 1.0
Author: M.Sevimli
Author URI: https://plife.se
License: M
*/
if( ! defined('ABSPATH') ) {
    exit;
}
class reklamshopEditor {
    public function __construct()
    {
        add_action('wp_enqueue_scripts',array($this,'require_files'));
        add_action('the_content',array($this,'hook_editor'));
        //add_action('reklamshop_editor',array($this,'init_editor'));
        add_action('reklamshop_editor_full',array($this,'init_editor'));
        add_action( 'wp_head', array($this,'storefront_remove_storefront_breadcrumbs'));

    }
    function require_files() {
        if ( is_page('editor') ) {
            $dataToBePassed = array(
                'home'            => get_stylesheet_directory_uri(),
                'plugins_url'   => plugins_url('assets/fonts/', __FILE__)
            );

            wp_enqueue_style('reklamshop-editor-style', plugins_url('assets/css/reklamshop-editor.css', __FILE__), true, 1.0);
            wp_enqueue_style('dropzone-css', plugins_url('assets/css/dropzone.min.css', __FILE__), true, 5.7);
            wp_enqueue_style('animate-css', plugins_url('assets/css/animate.min.css', __FILE__), true, 4.0);
            wp_enqueue_style('modal-css', plugins_url('assets/css/modal.css', __FILE__), true, 1.0);

            wp_enqueue_script('fabric-js', plugins_url('assets/js/fabric.min.js', __FILE__), true, 3.6);
            wp_enqueue_script('dropzone-js', plugins_url('assets/js/dropzone.min.js', __FILE__), true, 5.7);
            wp_enqueue_script('reklamshop-editor-js', plugins_url('assets/js/reklamshop-editor.js', __FILE__), true, 1.0);
            wp_enqueue_script('reklamshop-editor-modal-js', plugins_url('assets/js/modal.js', __FILE__), true, 1.0,true);

            wp_enqueue_script('font-face-observer', plugins_url('assets/js/fontfaceobserver.js', __FILE__), true, 2.1);
            wp_enqueue_script('open-type-js', plugins_url('assets/js/opentype.min.js', __FILE__), true, 1.3);

            wp_enqueue_script('fabric-curves-text', plugins_url('assets/js/fabric.CurvesText.min.js', __FILE__), true, 0.9);
            wp_enqueue_script('footer-required-js', plugins_url('assets/js/footer-required-js.js', __FILE__), array(), 1.0, true);
            wp_localize_script( 'footer-required-js', 'php_vars', $dataToBePassed );
        }
    }
    function hook_editor() {
        if(is_page('editor')) {
            do_action('reklamshop_editor');
        }
    }
    function storefront_remove_storefront_breadcrumbs() {
        if(is_page('editor')) {
            remove_action('storefront_before_content', 'woocommerce_breadcrumb', 10);
        }
    }
    function init_editor() {
        ?>
        <div class="reklamshop-editor-cover">
            <div class="editor-tools-side">
                <div class="editor-tools-row-head"></div>
                <div class="editor-tools-row">
                    <div class="editor-tools-item tool-active" data="selection">
                        <i class="fas fa-mouse-pointer"></i>
                    </div>
                    <div class="editor-tools-item" data="multi-selection">
                        <i class="far fa-object-ungroup"></i>
                    </div>
                </div>
                <div class="editor-tools-row">
                    <div class="editor-tools-item" data="zoom">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="editor-tools-item" data="drag">
                        <i class="fas fa-hand-rock"></i>
                    </div>
                </div>
                <div class="editor-tools-row">
                    <div class="editor-tools-item" data="line">
                        <i class="fas fa-minus"></i>
                    </div>
                    <div class="editor-tools-item" data="brush">
                        <i class="fas fa-paint-brush"></i>
                    </div>
                </div>
                <div class="editor-tools-row">
                    <div class="editor-tools-item" data="rect">
                        <i class="fas fa-object-ungroup"></i>
                    </div>
                    <div class="editor-tools-item" data="circle">
                        <i class="far fa-circle"></i>
                    </div>
                </div>
                <div class="editor-tools-row">
                    <div class="editor-tools-item" data="text">
                        <i class="fas fa-font"></i>
                    </div>
                    <div class="editor-tools-item" data="remove">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                </div>
            </div>

            <div class="reklamshop-editor-inside">
                <div class="editor-tools-top">
                    <div class="editor-tools-grid"></div>
                    <div class="editor-tools-grid"><input type="color"></div>
                    <div class="editor-tools-grid">
                        <select>
                            <option>test 1</option>
                            <option>test 2</option>
                            <option>test 3</option>
                        </select>
                    </div>
                    <div class="editor-tools-grid">
                        <label>Document </label>
                        <input type="number" min="1" class="editor-tools-input">
                        <span>x</span>
                        <input type="number" min="1" class="editor-tools-input">
                    </div>
                    <div class="editor-tools-grid">
                        <label>Zoom</label>
                        <input type="range" id="zoom-bar" name="zoom-bar" value="1" min="0.1" max="10.1">
                    </div>
                    <div class="editor-tools-grid">
                        <div class="editor-full-screen-ico">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </div>
                    </div>
                </div>

                <div class="reklamshop-editor-area" id="reklamshop-editor-area">
                    <canvas id="magicEditor" height="400"></canvas>
                    <div id="editor-control-panel" class="editor-control-panel">
                        <div class="ecp-row">
                            <div class="ecp-icon">
                                <input type="color" id="ecp-color-control" class="ecp-color-control">
                            </div>
                            <div class="ecp-icon" id="ecp-text-align-left">
                                <i class="fas fa-align-left"></i>
                            </div>
                            <div class="ecp-icon" id="ecp-text-align-center">
                                <i class="fas fa-align-center"></i>
                            </div>
                            <div class="ecp-icon" id="ecp-text-align-right">
                                <i class="fas fa-align-right"></i>
                            </div>
                            <div class="ecp-icon" id="ecp-font-bold">
                                <i class="fas fa-bold"></i>
                            </div>
                            <div class="ecp-icon" id="ecp-font-italic">
                                <i class="fas fa-italic"></i>
                            </div>
                        </div>
                        <hr>
                        <div class="ecp-row">
                            <div class="ecp-icon">
                                <input type="number" id="ecp-font-size" class="ecp-font-size" value="14">
                            </div>
                            <div class="ecp-icon">
                                <select id="ecp-font-family" class="ecp-font-family">
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="ecp-row">
                            <div class="ecp-icon" id="ecp-arrange-flip-front" >
                                <span class="material-icons">
                                    flip_to_front
                                </span>
                            </div>
                            <div class="ecp-icon" id="ecp-arrange-flip-back">
                                <span class="material-icons">
                                    flip_to_back
                                </span>
                            </div>
                            <div class="ecp-icon" id="ecp-arrange-send-backward">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="isolation:isolate" viewBox="0 0 24 24" width="24" height="24"><defs><clipPath id="_clipPath_PGUVCxWHiS0fJmALjiYeT6SkjgvTWa8a"><rect width="24" height="24"/></clipPath></defs><g clip-path="url(#_clipPath_PGUVCxWHiS0fJmALjiYeT6SkjgvTWa8a)"><rect width="24" height="24" style="fill:rgb(35,40,45)" fill-opacity="0"/><path d="M 6.858 5.636 L 17.142 5.636 C 17.816 5.636 18.364 6.184 18.364 6.858 L 18.364 17.142 C 18.364 17.816 17.816 18.364 17.142 18.364 L 6.858 18.364 C 6.184 18.364 5.636 17.816 5.636 17.142 L 5.636 6.858 C 5.636 6.184 6.184 5.636 6.858 5.636 Z" style="stroke:none;fill:#B9B9B9;stroke-miterlimit:10;"/><path d="M 3.342 2 L 9.658 2 C 10.399 2 11 2.601 11 3.342 L 11 9.658 C 11 10.399 10.399 11 9.658 11 L 3.342 11 C 2.601 11 2 10.399 2 9.658 L 2 3.342 C 2 2.601 2.601 2 3.342 2 Z" style="fill:none;stroke:#8B8B8B;stroke-width:0.5;stroke-linecap:square;stroke-dasharray:1;stroke-miterlimit:2;"/><path d="M 14.342 12 L 20.658 12 C 21.399 12 22 12.601 22 13.342 L 22 19.658 C 22 20.399 21.399 21 20.658 21 L 14.342 21 C 13.601 21 13 20.399 13 19.658 L 13 13.342 C 13 12.601 13.601 12 14.342 12 Z" style="fill:none;stroke:#8B8B8B;stroke-width:0.5;stroke-linecap:square;stroke-dasharray:1;stroke-miterlimit:2;"/></g></svg>
                            </div>
                            <div class="ecp-icon" id="ecp-arrange-bring-forward">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="isolation:isolate" viewBox="0 0 24 24" width="24" height="24"><defs><clipPath id="_clipPath_4iNdfbD8YrGTuYA6gmRSSNYtB7JQyKwp"><rect width="24" height="24"/></clipPath></defs><g clip-path="url(#_clipPath_4iNdfbD8YrGTuYA6gmRSSNYtB7JQyKwp)"><rect x="1" y="5" width="18" height="18" transform="matrix(1,0,0,1,0,0)" fill="rgb(232,232,232)"/><rect x="3" y="3" width="18" height="18" transform="matrix(1,0,0,1,0,0)" fill="rgb(174,171,171)"/><rect x="5" y="1" width="18" height="18" transform="matrix(1,0,0,1,0,0)" fill="rgb(235,235,235)"/></g></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
      
            <div class="add-ons-buttons">
                <div class="font-button add-on-button" data="font-button">
                    <span>Fonts</span>
                </div>
                <div class="shapes-button add-on-button" data="shapes-button">
                    <span>Shapes</span>
                </div>
                <div class="upload-button add-on-button" data="upload-button">
                    <span>Upload</span>
                </div>
            </div>
            <div class="add-ons-cover">
                <div class="add-ons-close">
                    <i class="fas fa-angle-double-right"></i>
                </div>
                <div class="fonts-container add-ons-container">
                    <div class="sample-font" data="times">
                        <span>Sample Text</span>
                    </div>
                    <div class="sample-font" data="notable">
                        <span>Sample Text</span>
                    </div>
                    <div class="sample-font" data="piedra">
                        <span>Sample Text</span>
                    </div>
                    <div class="sample-font" data="MuseoModerno">
                        <span>Sample Text</span>
                    </div>
                    <div class="sample-font" data="Pangolin">
                        <span>Sample Text</span>
                    </div>
                </div>
                <div class="shapes-container add-ons-container">
                    <div class="add-ons-group">
                        <div class="add-ons-group-title" data="discount">
                            Discount
                        </div>
                        <div class="add-ons-group-container" data="discount">
                            <div class="shapes-obj" id="50perDiscount">
                                <svg width="46.278mm" height="46.278mm" version="1.1" viewBox="0 0 46.278 46.278" xmlns="http://www.w3.org/2000/svg" xmlns:src="http://creativecommons.org/ns#" xmlns:src="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
                                    <!--svg  width="46.278mm" height="46.278mm" version="1.1" viewBox="0 0 46.278 46.278" xmlns="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"!-->
                                    <defs>
                                        <linearGradient id="linearGradient4234" x2="1" gradientTransform="matrix(92.758 92.758 92.758 -92.758 238.37 351.95)" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#e81334" offset="0"/>
                                            <stop stop-color="#d7132f" offset="1"/>
                                        </linearGradient>
                                        <clipPath id="clipPath4244">
                                            <path d="m0 500h750v-500h-750z"/>
                                        </clipPath>
                                        <clipPath id="clipPath4256">
                                            <path d="m226.38 456.7h116.09v-116.09h-116.09z"/>
                                        </clipPath>
                                        <clipPath id="clipPath4260">
                                            <path d="m314.65 456.7-88.273-88.273c3.49-6.8 8.128-12.91 13.666-18.073l92.68 92.681c-5.163 5.538-11.273 10.174-18.073 13.665"/>
                                        </clipPath>
                                        <mask id="mask4272" x="0" y="0" width="1" height="1" maskUnits="userSpaceOnUse">
                                            <path d="m-32768 32767h65535v-65535h-65535z" fill="url(#linearGradient4270)"/>
                                        </mask>
                                        <linearGradient id="linearGradient4270" x2="1" gradientTransform="matrix(106.35 0 0 -106.35 226.38 403.52)" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#fff" stop-opacity="0" offset="0"/>
                                            <stop stop-color="#fff" offset="1"/>
                                        </linearGradient>
                                        <linearGradient id="linearGradient4288" x2="1" gradientTransform="matrix(106.35 0 0 -106.35 226.38 403.52)" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#fff" offset="0"/>
                                            <stop stop-color="#fff" offset="1"/>
                                        </linearGradient>
                                        <clipPath id="clipPath4292">
                                            <path d="m342.47 429.5c-1.827 3.376-3.953 6.562-6.322 9.548l-92.119-92.119c2.985-2.369 6.172-4.495 9.547-6.322z"/>
                                        </clipPath>
                                        <mask id="mask4304" x="0" y="0" width="1" height="1" maskUnits="userSpaceOnUse">
                                            <path d="m-32768 32767h65535v-65535h-65535z" fill="url(#linearGradient4302)"/>
                                        </mask>
                                        <linearGradient id="linearGradient4302" x2="1" gradientTransform="matrix(98.441 0 0 -98.441 244.03 389.83)" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#fff" stop-opacity="0" offset="0"/>
                                            <stop stop-color="#fff" offset="1"/>
                                        </linearGradient>
                                        <linearGradient id="linearGradient4320" x2="1" gradientTransform="matrix(98.441 0 0 -98.441 244.03 389.83)" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#fff" offset="0"/>
                                            <stop stop-color="#fff" offset="1"/>
                                        </linearGradient>
                                    </defs>
                                    <metadata>
                                        <rdf:RDF>
                                            <cc:Work rdf:about="">
                                                <dc:format>image/svg+xml</dc:format>
                                                <dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"/>
                                                <dc:title/>
                                            </cc:Work>
                                        </rdf:RDF>
                                    </metadata>
                                    <g transform="matrix(.87661 0 0 .87661 2.1582 1.8849)">
                                        <g transform="matrix(.35278 0 0 -.35278 -75.557 164.2)">
                                            <path d="m219.16 398.33c0-36.225 29.366-65.591 65.59-65.591 36.225 0 65.591 29.366 65.591 65.591 0 36.224-29.366 65.59-65.591 65.59-36.224 0-65.59-29.366-65.59-65.59" fill="url(#linearGradient4234)"/>
                                        </g>
                                        <g transform="matrix(.35278 0 0 -.35278 -75.557 164.2)">
                                            <g clip-path="url(#clipPath4244)">
                                                <g transform="translate(284.75 338.92)">
                                                    <path d="m0 0c32.755 0 59.404 26.648 59.404 59.404 0 32.755-26.649 59.404-59.404 59.404-32.756 0-59.404-26.649-59.404-59.404 0-32.756 26.648-59.404 59.404-59.404z" fill="none" stroke="#fff" stroke-miterlimit="10" stroke-width="2"/>
                                                </g>
                                                <g clip-path="url(#clipPath4256)" opacity=".10001">
                                                    <g clip-path="url(#clipPath4260)">
                                                        <g mask="url(#mask4272)">
                                                            <path d="m314.65 456.7-88.273-88.273c3.49-6.8 8.128-12.91 13.666-18.073l92.68 92.681c-5.163 5.538-11.273 10.174-18.073 13.665" fill="url(#linearGradient4288)"/>
                                                        </g>
                                                    </g>
                                                    <g clip-path="url(#clipPath4292)">
                                                        <g mask="url(#mask4304)">
                                                            <path d="m342.47 429.5c-1.827 3.376-3.953 6.562-6.322 9.548l-92.119-92.119c2.985-2.369 6.172-4.495 9.547-6.322z" fill="url(#linearGradient4320)"/>
                                                        </g>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                        <text x="6.5554967" y="30.816315" fill="#ffffff" font-family="'Poppins ExtraBold'" font-size="16.572px" font-weight="800" stroke-width=".35278"><tspan x="6.5554967 17.410492 28.29863" y="30.816315" stroke-width=".35278">50%</tspan></text>
                                        <text x="16.671013" y="13.79274" fill="#ffffff" font-family="'Lobster Two'" font-size="7.0769px" font-style="italic" font-weight="bold" stroke-width=".35278"><tspan x="16.671013 22.120247 25.432259 26.953796 30.584267" y="13.79274">Up To</tspan><tspan x="20.980873 25.91349" y="39.022003">Of</tspan></text>
                                    </g>
                                </svg>
                            </div>
                            <div class="shapes-obj" id="testSVG">
                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                     width="105.082px" height="105.082px" viewBox="0 0 105.082 105.082" enable-background="new 0 0 105.082 105.082"
                                     xml:space="preserve">
                            <circle fill="#594A42" stroke="#231F20" stroke-width="0.4904" stroke-miterlimit="10" cx="52.541" cy="52.541" r="52.296"/>
                                    <text transform="matrix(1 0 0 1 6.2666 58.8047)" fill="#FFFFFF" font-family="'MyriadPro-Regular'" font-size="21.0694">This is test</text>
                        </svg>
                            </div>

                        </div>
                        </div>
                    </div>
                <div class="upload-container add-ons-container">
                    <div class="upload-button-cover">
                        <input type="button" id="upload-image" value="Image" class="green-btn">
                    </div>
                    <div class="upload-button-cover">
                        <input type="button" value="Font" class="green-btn">
                    </div>

                </div>
            </div>
        </div>
        <input type="button" value="toSVG" onclick="do_save()">
        <div id="dwn"></div>

        <!-- The Modal -->
        <div id="upload-modal" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <div class="drop-file-cover">
                    <form action="#" class="dropzone" id="myAwesomeDropzone">
                        <div class="fallback">
                            <input name="file" type="file" multiple />
                        </div>
                    </form>
                </div>
            </div>

        </div>
        <?php
    }
}

new reklamshopEditor();