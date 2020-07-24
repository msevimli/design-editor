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
        add_action('reklamshop_editor',array($this,'init_editor'));
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

            wp_enqueue_script('fabric-js', plugins_url('assets/js/fabric.min.js', __FILE__), true, 3.6);
            wp_enqueue_script('dropzone-js', plugins_url('assets/js/dropzone.min.js', __FILE__), true, 5.7);
            wp_enqueue_script('reklamshop-editor-js', plugins_url('assets/js/reklamshop-editor.js', __FILE__), true, 1.0);

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
    function init_editor() {
        ?>
        <span class="dashicons dashicons-editor-expand"></span>
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
            <div class="upload-button-area">

                <div id="upload-menu-button">

                    <i class="fas fa-upload"></i><span> Upload</span>
                </div>
                <div class="upload-buttons-container">
                    <div class="upload-button-cover">
                        <input type="button" value="Image" class="green-btn">
                    </div>
                    <div class="upload-button-cover">
                        <input type="button" value="Font" class="green-btn">
                    </div>
                </div>
            </div>
        </div>
        <input type="button" value="toSVG" onclick="do_save()">
        <div id="dwn"></div>
      <br />
            <section class="container">
                <div class="color-pickers"></div>
                <button id="colorPicker">Add another pickr</button>
            </section>
        <br />

        <button id="rect">Rec</button>
        <button id="circle">Circle</button>
        <button id="arrow">Arrow</button>
        <button id="freedrawing">Free Drawing</button>
        <input type="file" id="myfile" name="myfile" accept="image/png, image/jpeg">

        <form action="#" class="dropzone" id="myAwesomeDropzone">
            <div class="fallback">
                <input name="file" type="file" multiple />
            </div>
        </form>
        <?php
    }
}

new reklamshopEditor();