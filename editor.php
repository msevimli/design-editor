<?php
/*
Plugin Name: Reklamshop Editor
Plugin URI: https://reklamshop.se/
Description:  This plugin design editor for Reklamshop
Version: 1.0
Author: M.Sevimli
Author URI: https://plife.se
License: MIT
Date : Juli 2020 - Present
*/
if (!defined('ABSPATH')) {
    exit;
}

class reklamshopEditor
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'require_files'));
        add_action('the_content', array($this, 'hook_editor'));
        //add_action('reklamshop_editor',array($this,'init_editor'));
        add_action('reklamshop_editor_full', array($this, 'init_editor'));
        add_action('wp_head', array($this, 'storefront_remove_storefront_breadcrumbs'));

    }

    function require_files()
    {
        if (is_page('editor')) {
            $dataToBePassed = array(
                'home' => get_stylesheet_directory_uri(),
                'plugins_url' => plugins_url('assets/fonts/', __FILE__)
            );

            wp_enqueue_style('reklamshop-editor-style', plugins_url('assets/css/reklamshop-editor.css', __FILE__), true, 1.0);
            wp_enqueue_style('dropzone-css', plugins_url('assets/css/dropzone.min.css', __FILE__), true, 5.7);
            wp_enqueue_style('animate-css', plugins_url('assets/css/animate.min.css', __FILE__), true, 4.0);
            wp_enqueue_style('modal-css', plugins_url('assets/css/modal.css', __FILE__), true, 1.0);

            wp_enqueue_script('fabric-js', plugins_url('assets/js/fabric.min.js', __FILE__), true, 3.6);
            wp_enqueue_script('dropzone-js', plugins_url('assets/js/dropzone.min.js', __FILE__), true, 5.7);
            wp_enqueue_script('reklamshop-editor-js', plugins_url('assets/js/reklamshop-editor.js', __FILE__), true, 1.0);
            wp_enqueue_script('reklamshop-editor-modal-js', plugins_url('assets/js/modal.js', __FILE__), true, 1.0, true);

            wp_enqueue_script('font-face-observer', plugins_url('assets/js/fontfaceobserver.js', __FILE__), true, 2.1);
            wp_enqueue_script('open-type-js', plugins_url('assets/js/opentype.min.js', __FILE__), true, 1.3);

            wp_enqueue_script('fabric-curves-text', plugins_url('assets/js/fabric.CurvesText.min.js', __FILE__), true, 0.9);
            wp_enqueue_script('footer-required-js', plugins_url('assets/js/footer-required-js.js', __FILE__), array(), 1.0, true);
            wp_localize_script('footer-required-js', 'php_vars', $dataToBePassed);
        }
    }

    function hook_editor()
    {
        if (is_page('editor')) {
            do_action('reklamshop_editor');
        }
    }

    function storefront_remove_storefront_breadcrumbs()
    {
        if (is_page('editor')) {
            remove_action('storefront_before_content', 'woocommerce_breadcrumb', 10);
        }
    }

    function init_editor()
    {
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
                    <div class="editor-tools-grid document-color-container editor-tools-container">
                        Document Color
                        <input type="color" id="set-background-color" value="#ffffff">
                    </div>
                    <div class="editor-tools-grid document-size-container editor-tools-container">
                        <label>Document </label>
                        <input id="document-width" type="number" min="1" class="editor-tools-input document-size-input">
                        <span>x</span>
                        <input id="document-height" type="number" min="1" class="editor-tools-input document-size-input">
                        Unit:
                        <select>
                            <option>px</option>
                            <option>mm</option>
                            <option>cm</option>
                        </select>
                    </div>
                    <div class="editor-tools-grid editor-tools-container">
                        <label>Zoom</label>
                        <input type="range" id="zoom-bar" name="zoom-bar" value="1" min="0.1" max="10.1">
                    </div>
                    <div class="editor-tools-grid editor-tools-container">
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
                            <div class="ecp-icon" id="ecp-arrange-flip-front">
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
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                     style="isolation:isolate" viewBox="0 0 24 24" width="24" height="24">
                                    <defs>
                                        <clipPath id="_clipPath_PGUVCxWHiS0fJmALjiYeT6SkjgvTWa8a">
                                            <rect width="24" height="24"/>
                                        </clipPath>
                                    </defs>
                                    <g clip-path="url(#_clipPath_PGUVCxWHiS0fJmALjiYeT6SkjgvTWa8a)">
                                        <rect width="24" height="24" style="fill:rgb(35,40,45)" fill-opacity="0"/>
                                        <path d="M 6.858 5.636 L 17.142 5.636 C 17.816 5.636 18.364 6.184 18.364 6.858 L 18.364 17.142 C 18.364 17.816 17.816 18.364 17.142 18.364 L 6.858 18.364 C 6.184 18.364 5.636 17.816 5.636 17.142 L 5.636 6.858 C 5.636 6.184 6.184 5.636 6.858 5.636 Z"
                                              style="stroke:none;fill:#B9B9B9;stroke-miterlimit:10;"/>
                                        <path d="M 3.342 2 L 9.658 2 C 10.399 2 11 2.601 11 3.342 L 11 9.658 C 11 10.399 10.399 11 9.658 11 L 3.342 11 C 2.601 11 2 10.399 2 9.658 L 2 3.342 C 2 2.601 2.601 2 3.342 2 Z"
                                              style="fill:none;stroke:#8B8B8B;stroke-width:0.5;stroke-linecap:square;stroke-dasharray:1;stroke-miterlimit:2;"/>
                                        <path d="M 14.342 12 L 20.658 12 C 21.399 12 22 12.601 22 13.342 L 22 19.658 C 22 20.399 21.399 21 20.658 21 L 14.342 21 C 13.601 21 13 20.399 13 19.658 L 13 13.342 C 13 12.601 13.601 12 14.342 12 Z"
                                              style="fill:none;stroke:#8B8B8B;stroke-width:0.5;stroke-linecap:square;stroke-dasharray:1;stroke-miterlimit:2;"/>
                                    </g>
                                </svg>
                            </div>
                            <div class="ecp-icon" id="ecp-arrange-bring-forward">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                     style="isolation:isolate" viewBox="0 0 24 24" width="24" height="24">
                                    <defs>
                                        <clipPath id="_clipPath_4iNdfbD8YrGTuYA6gmRSSNYtB7JQyKwp">
                                            <rect width="24" height="24"/>
                                        </clipPath>
                                    </defs>
                                    <g clip-path="url(#_clipPath_4iNdfbD8YrGTuYA6gmRSSNYtB7JQyKwp)">
                                        <rect x="1" y="5" width="18" height="18" transform="matrix(1,0,0,1,0,0)"
                                              fill="rgb(232,232,232)"/>
                                        <rect x="3" y="3" width="18" height="18" transform="matrix(1,0,0,1,0,0)"
                                              fill="rgb(174,171,171)"/>
                                        <rect x="5" y="1" width="18" height="18" transform="matrix(1,0,0,1,0,0)"
                                              fill="rgb(235,235,235)"/>
                                    </g>
                                </svg>
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
                            <i class="group-title-ico fas fa-sort-down"></i>
                        </div>
                        <div class="add-ons-group-container" data="discount">
                            <div class="shapes-obj" id="50perDiscount">
                                <svg width="46.278mm" height="46.278mm" version="1.1" viewBox="0 0 46.278 46.278"
                                     xmlns="http://www.w3.org/2000/svg" xmlns:src="http://creativecommons.org/ns#"
                                     xmlns:src="http://purl.org/dc/elements/1.1/"
                                     xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
                                    <!--svg  width="46.278mm" height="46.278mm" version="1.1" viewBox="0 0 46.278 46.278" xmlns="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"!-->
                                    <defs>
                                        <linearGradient id="linearGradient4234" x2="1"
                                                        gradientTransform="matrix(92.758 92.758 92.758 -92.758 238.37 351.95)"
                                                        gradientUnits="userSpaceOnUse">
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
                                            <path d="m-32768 32767h65535v-65535h-65535z"
                                                  fill="url(#linearGradient4270)"/>
                                        </mask>
                                        <linearGradient id="linearGradient4270" x2="1"
                                                        gradientTransform="matrix(106.35 0 0 -106.35 226.38 403.52)"
                                                        gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#fff" stop-opacity="0" offset="0"/>
                                            <stop stop-color="#fff" offset="1"/>
                                        </linearGradient>
                                        <linearGradient id="linearGradient4288" x2="1"
                                                        gradientTransform="matrix(106.35 0 0 -106.35 226.38 403.52)"
                                                        gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#fff" offset="0"/>
                                            <stop stop-color="#fff" offset="1"/>
                                        </linearGradient>
                                        <clipPath id="clipPath4292">
                                            <path d="m342.47 429.5c-1.827 3.376-3.953 6.562-6.322 9.548l-92.119-92.119c2.985-2.369 6.172-4.495 9.547-6.322z"/>
                                        </clipPath>
                                        <mask id="mask4304" x="0" y="0" width="1" height="1" maskUnits="userSpaceOnUse">
                                            <path d="m-32768 32767h65535v-65535h-65535z"
                                                  fill="url(#linearGradient4302)"/>
                                        </mask>
                                        <linearGradient id="linearGradient4302" x2="1"
                                                        gradientTransform="matrix(98.441 0 0 -98.441 244.03 389.83)"
                                                        gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#fff" stop-opacity="0" offset="0"/>
                                            <stop stop-color="#fff" offset="1"/>
                                        </linearGradient>
                                        <linearGradient id="linearGradient4320" x2="1"
                                                        gradientTransform="matrix(98.441 0 0 -98.441 244.03 389.83)"
                                                        gradientUnits="userSpaceOnUse">
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
                                            <path d="m219.16 398.33c0-36.225 29.366-65.591 65.59-65.591 36.225 0 65.591 29.366 65.591 65.591 0 36.224-29.366 65.59-65.591 65.59-36.224 0-65.59-29.366-65.59-65.59"
                                                  fill="url(#linearGradient4234)"/>
                                        </g>
                                        <g transform="matrix(.35278 0 0 -.35278 -75.557 164.2)">
                                            <g clip-path="url(#clipPath4244)">
                                                <g transform="translate(284.75 338.92)">
                                                    <path d="m0 0c32.755 0 59.404 26.648 59.404 59.404 0 32.755-26.649 59.404-59.404 59.404-32.756 0-59.404-26.649-59.404-59.404 0-32.756 26.648-59.404 59.404-59.404z"
                                                          fill="none" stroke="#fff" stroke-miterlimit="10"
                                                          stroke-width="2"/>
                                                </g>
                                                <g clip-path="url(#clipPath4256)" opacity=".10001">
                                                    <g clip-path="url(#clipPath4260)">
                                                        <g mask="url(#mask4272)">
                                                            <path d="m314.65 456.7-88.273-88.273c3.49-6.8 8.128-12.91 13.666-18.073l92.68 92.681c-5.163 5.538-11.273 10.174-18.073 13.665"
                                                                  fill="url(#linearGradient4288)"/>
                                                        </g>
                                                    </g>
                                                    <g clip-path="url(#clipPath4292)">
                                                        <g mask="url(#mask4304)">
                                                            <path d="m342.47 429.5c-1.827 3.376-3.953 6.562-6.322 9.548l-92.119-92.119c2.985-2.369 6.172-4.495 9.547-6.322z"
                                                                  fill="url(#linearGradient4320)"/>
                                                        </g>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                        <text x="6.5554967" y="30.816315" fill="#ffffff"
                                              font-family="'Poppins ExtraBold'" font-size="16.572px" font-weight="800"
                                              stroke-width=".35278">
                                            <tspan x="6.5554967 17.410492 28.29863" y="30.816315" stroke-width=".35278">
                                                50%
                                            </tspan>
                                        </text>
                                        <text x="16.671013" y="13.79274" fill="#ffffff" font-family="'Lobster Two'"
                                              font-size="7.0769px" font-style="italic" font-weight="bold"
                                              stroke-width=".35278">
                                            <tspan x="16.671013 22.120247 25.432259 26.953796 30.584267" y="13.79274">Up
                                                To
                                            </tspan>
                                            <tspan x="20.980873 25.91349" y="39.022003">Of</tspan>
                                        </text>
                                    </g>
                                </svg>
                            </div>
                            <div class="shapes-obj" id="testSVG">
                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                     xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                     width="105.082px" height="105.082px" viewBox="0 0 105.082 105.082"
                                     enable-background="new 0 0 105.082 105.082"
                                     xml:space="preserve">
                                    <circle fill="#594A42" stroke="#231F20" stroke-width="0.4904" stroke-miterlimit="10"
                                            cx="52.541" cy="52.541" r="52.296"/>
                                    <text transform="matrix(1 0 0 1 6.2666 58.8047)" fill="#FFFFFF"
                                          font-family="'MyriadPro-Regular'" font-size="21.0694">This is test
                                    </text>
                                </svg>
                            </div>
                            <div class="shapes-obj" id="offer">
                                
                            </div>
                        </div>
                    </div>
                    <div class="add-ons-group">
                        <div class="add-ons-group-title" data="comment">
                            Comment
                            <i class="group-title-ico fas fa-sort-down"></i>
                        </div>
                        <div class="add-ons-group-container" data="comment">
                            <div class="shapes-obj" id="comment-1">
                                <svg version="1.1" id="Layer_1" xmlns:x="&ns_extend;" xmlns:i="&ns_ai;" xmlns:graph="&ns_graphs;"
                                     xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="236.473px"
                                     height="231.965px" viewBox="0 0 236.473 231.965" enable-background="new 0 0 236.473 231.965" xml:space="preserve">
<metadata>
    <sfw  xmlns="&ns_sfw;">
        <slices></slices>
        <sliceSourceBounds  width="-32766" height="-32766" x="8498" y="24970" bottomLeftOrigin="true"></sliceSourceBounds>
    </sfw>
</metadata>
                                    <g>
                                        <g>
                                            <g>
                                                <path fill="#F9BB30" d="M181.123,32.036C134.757,4.08,74.506,19.005,46.549,65.372c-14.698,24.379-17.536,52.594-10.188,77.937
				c0.797,2.75,0.423,5.705-1.043,8.164L8.525,196.415c-2.281,3.825,1.63,8.349,5.744,6.642
				c11.584-4.808,28.485-11.819,39.952-16.562c3.396-1.403,7.298-0.729,10.007,1.753c4.729,4.33,9.954,8.26,15.657,11.698
				c46.366,27.956,106.617,13.031,134.573-33.335C242.498,120.107,227.628,60.075,181.123,32.036z"/>
                                                <path fill="#FFFFFF" d="M102.842,203.856c-0.271,0-0.545-0.042-0.816-0.131c-6.653-2.165-13.093-5.112-19.139-8.758
				c-1.246-0.751-1.647-2.371-0.896-3.617c0.751-1.246,2.371-1.647,3.617-0.896c5.703,3.438,11.776,6.218,18.049,8.261
				c1.384,0.45,2.141,1.937,1.69,3.321C104.985,203.149,103.952,203.856,102.842,203.856z"/>
                                                <path fill="#FFFFFF" d="M116.959,207.207c-0.133,0-0.267-0.011-0.402-0.031c-1.329-0.203-2.675-0.439-3.997-0.701
				c-1.428-0.282-2.356-1.669-2.073-3.097s1.67-2.358,3.098-2.073c1.247,0.247,2.516,0.469,3.77,0.661
				c1.438,0.22,2.427,1.564,2.207,3.003C119.361,206.272,118.238,207.207,116.959,207.207z"/>
                                                <path fill="#FFFFFF" d="M130.437,208.226c-1.122,0-2.244-0.021-3.372-0.062c-1.455-0.053-2.591-1.275-2.537-2.729
				c0.053-1.454,1.289-2.576,2.729-2.538c31.614,1.16,61.393-14.945,77.71-42.01c0.752-1.247,2.371-1.647,3.618-0.896
				s1.647,2.371,0.896,3.618C192.756,191.348,162.698,208.226,130.437,208.226z"/>
                                                <path fill="#F9BB30" d="M26.501,101.262c-0.144,0-0.29-0.013-0.437-0.037c-1.436-0.239-2.405-1.597-2.166-3.033
				c1.307-7.838,3.493-15.547,6.499-22.915c0.55-1.347,2.088-1.996,3.436-1.444c1.349,0.55,1.995,2.088,1.444,3.436
				c-2.858,7.005-4.938,14.336-6.18,21.789C28.883,100.348,27.766,101.262,26.501,101.262z"/>
                                                <path fill="#F9BB30" d="M130.408,224.06c-1.747,0-3.493-0.042-5.228-0.126c-1.454-0.07-2.575-1.306-2.505-2.76
				s1.309-2.571,2.76-2.505c3.119,0.15,6.283,0.159,9.402,0.025c1.456-0.067,2.685,1.063,2.747,2.519
				c0.063,1.454-1.065,2.684-2.52,2.747C133.52,224.027,131.964,224.06,130.408,224.06z"/>
                                                <path fill="#F9BB30" d="M143.935,223.19c-1.306,0-2.438-0.969-2.61-2.298c-0.187-1.443,0.833-2.765,2.276-2.951
				c9.876-1.276,19.498-3.966,28.599-7.994c1.332-0.587,2.888,0.014,3.477,1.344c0.59,1.331-0.012,2.888-1.343,3.477
				c-9.564,4.233-19.678,7.061-30.058,8.401C144.161,223.183,144.047,223.19,143.935,223.19z"/>
                                                <path fill="#F9BB30" d="M197.883,37.534c-0.593,0-1.188-0.199-1.68-0.606c-1.218-1.009-2.476-2.003-3.739-2.955
				c-1.163-0.876-1.396-2.528-0.52-3.691c0.875-1.162,2.528-1.395,3.69-0.52c1.329,1.001,2.651,2.046,3.932,3.107
				c1.121,0.929,1.276,2.59,0.347,3.711C199.393,37.209,198.641,37.534,197.883,37.534z"/>
                                                <path fill="#F9BB30" d="M184.934,28.349c-0.463,0-0.933-0.123-1.357-0.379c-2.873-1.731-5.855-3.339-8.865-4.778
				c-1.313-0.627-1.868-2.201-1.24-3.514c0.627-1.314,2.201-1.87,3.514-1.241c3.163,1.511,6.296,3.2,9.313,5.019
				c1.246,0.751,1.647,2.371,0.896,3.618C186.698,27.894,185.826,28.349,184.934,28.349z"/>
                                                <path fill="#F9BB30" d="M40.224,64.195c-0.464,0-0.933-0.122-1.358-0.378c-1.246-0.751-1.647-2.372-0.896-3.618
				c25.278-41.926,75.723-61.664,122.67-48.001c1.398,0.407,2.201,1.87,1.795,3.267c-0.407,1.397-1.871,2.2-3.268,1.794
				C114.51,4.262,66.528,23.039,42.482,62.92C41.988,63.741,41.117,64.195,40.224,64.195z"/>
                                            </g>
                                            <g>
                                                <g>
                                                    <path fill="#F9BB30" d="M5.968,16.13c0,0.452,0.056,0.76,0.172,0.923l0.347,0.185l0.691-0.123
					c0.575-0.124,1.095-0.185,1.557-0.185c2.363,0,4.266,0.585,5.707,1.755c1.44,1.169,2.161,2.924,2.161,5.264
					c0,2.504-0.792,4.412-2.377,5.726c-1.586,1.314-3.503,1.97-5.751,1.97c-2.191,0-4.15-0.739-5.88-2.216S0,25.878,0,23.21
					c0-3.694,1.225-7.583,3.676-11.667c2.449-4.083,5.434-7.459,8.95-10.127C13.951,0.472,14.787,0,15.134,0
					c0.519,0.246,0.778,0.472,0.778,0.677c0,0.329-0.232,0.718-0.692,1.17c-3.286,2.996-5.65,5.654-7.091,7.972
					C6.688,12.139,5.968,14.243,5.968,16.13z M26.462,16.13c0,0.452,0.057,0.76,0.173,0.923l0.346,0.185l0.691-0.123
					c0.575-0.124,1.094-0.185,1.557-0.185c2.363,0,4.266,0.585,5.707,1.755c1.44,1.169,2.162,2.924,2.162,5.264
					c0,2.504-0.793,4.412-2.378,5.726c-1.587,1.314-3.502,1.97-5.751,1.97c-2.191,0-4.15-0.739-5.88-2.216s-2.594-3.549-2.594-6.218
					c0-3.694,1.224-7.583,3.675-11.667c2.449-4.083,5.433-7.459,8.949-10.127C34.445,0.472,35.281,0,35.627,0
					c0.52,0.246,0.778,0.472,0.778,0.677c0,0.329-0.23,0.718-0.691,1.17c-3.286,2.996-5.651,5.654-7.09,7.972
					C27.182,12.139,26.462,14.243,26.462,16.13z"/>
                                                </g>
                                                <g>
                                                    <path fill="#F9BB30" d="M230.506,215.835c0-0.452-0.057-0.76-0.173-0.924l-0.346-0.185l-0.691,0.123
					c-0.576,0.123-1.095,0.186-1.558,0.186c-2.363,0-4.265-0.586-5.707-1.755c-1.44-1.17-2.161-2.925-2.161-5.264
					c0-2.504,0.792-4.413,2.378-5.726c1.586-1.314,3.502-1.971,5.75-1.971c2.191,0,4.151,0.739,5.881,2.217
					c1.729,1.478,2.594,3.55,2.594,6.218c0,3.694-1.225,7.582-3.675,11.667c-2.45,4.083-5.434,7.459-8.95,10.127
					c-1.326,0.943-2.162,1.416-2.508,1.416c-0.52-0.246-0.778-0.473-0.778-0.677c0-0.329,0.231-0.719,0.691-1.17
					c3.286-2.997,5.651-5.654,7.091-7.973C229.786,219.827,230.506,217.723,230.506,215.835z M210.012,215.835
					c0-0.452-0.057-0.76-0.173-0.924l-0.346-0.185l-0.692,0.123c-0.576,0.123-1.094,0.186-1.557,0.186
					c-2.362,0-4.265-0.586-5.707-1.755c-1.439-1.17-2.162-2.925-2.162-5.264c0-2.504,0.794-4.413,2.379-5.726
					c1.586-1.314,3.502-1.971,5.75-1.971c2.191,0,4.15,0.739,5.88,2.217s2.595,3.55,2.595,6.218c0,3.694-1.225,7.582-3.676,11.667
					c-2.449,4.083-5.433,7.459-8.949,10.127c-1.326,0.943-2.162,1.416-2.508,1.416c-0.519-0.246-0.778-0.473-0.778-0.677
					c0-0.329,0.23-0.719,0.692-1.17c3.285-2.997,5.65-5.654,7.09-7.973C209.291,219.827,210.012,217.723,210.012,215.835z"/>
                                                </g>
                                            </g>
                                        </g>
                                        <text transform="matrix(1 0 0 1 83.4004 82.8179)" fill="#066D70" font-family="'MyriadPro-Bold'" font-size="29.669">QUOTE</text>
                                        <text transform="matrix(1 0 0 1 63.8223 130.1274)"><tspan x="0" y="0" font-family="'MyriadPro-Bold'" font-size="17">Lorem Ipsum text</tspan><tspan x="21.776" y="20.4" font-family="'MyriadPro-Bold'" font-size="17">second line </tspan></text>
                                    </g>
</svg>
                            </div>
                            <div class="shapes-obj" id="comment-2">
                                <svg
                                        xmlns:dc="http://purl.org/dc/elements/1.1/"
                                        xmlns:cc="http://creativecommons.org/ns#"
                                        xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                                        xmlns:svg="http://www.w3.org/2000/svg"
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="250"
                                        height="150"
                                        viewBox="0 0 66.145831 39.687501"
                                        version="1.1"
                                        id="svg8">
                                    <defs
                                            id="defs2">
                                        <rect
                                                x="3.7708997"
                                                y="3.8666402"
                                                width="56.08613"
                                                height="32.704604"
                                                id="rect1404" />
                                    </defs>
                                    <metadata
                                            id="metadata5">
                                        <rdf:RDF>
                                            <cc:Work
                                                    rdf:about="">
                                                <dc:format>image/svg+xml</dc:format>
                                                <dc:type
                                                        rdf:resource="http://purl.org/dc/dcmitype/StillImage" />
                                                <dc:title></dc:title>
                                            </cc:Work>
                                        </rdf:RDF>
                                    </metadata>
                                    <g
                                            id="layer1">
                                        <text
                                                xml:space="preserve"
                                                id="text1402"
                                                style="font-style:normal;font-weight:normal;font-size:10.5833px;line-height:0.92;font-family:sans-serif;white-space:pre;shape-inside:url(#rect1404);fill:#000000;fill-opacity:1;stroke:none;"
                                                transform="matrix(1.1816345,0,0,1.1816345,-0.72914671,-0.62552293)"><tspan
                                                    x="3.7714844"
                                                    y="11.484683"><tspan>first
                                                </tspan></tspan><tspan
                                                    x="3.7714844"
                                                    y="21.221319"><tspan>secon
                                                </tspan></tspan><tspan
                                                    x="3.7714844"
                                                    y="30.957955"><tspan>third</tspan></tspan></text>
                                    </g>
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
                            <input name="file" type="file" multiple/>
                        </div>
                    </form>
                </div>
            </div>

        </div>
        <?php
    }
}

new reklamshopEditor();