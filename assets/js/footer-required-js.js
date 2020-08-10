var fontsArr = [];

const magic = new magicEditor("magicEditor","reklamshop-editor-area");
jQuery(document).ready(function ($) {
   //"use strict";

    console.log( php_vars.home );
    console.log( php_vars.plugins_url );
    var text = new fabric.Textbox('Hello world From Magic Editor', {
        width:250,
        cursorColor :"blue",
        top:10,
        left:10,
        fontSize:24
    });
    magic.editor.add(text);
    magic.resizeIt(500,300);

    magic.editor.on('mouse:down', function(options) {
        if (options.target) {
            var obj= options.target;
            console.log('an object was clicked! ', obj.type);
            console.log(obj.left + "," + obj.top);
        } else {
            console.log('clicked canvas');
        }
    });

    var obj = ''
    function addObj(objj) {
        obj = objj;
        if(obj=='remove') {
            deleteObjects();
        }
    }

    var canvas = magic.editor;
    canvas.isDrawingMode = false;
    canvas.selection= true;
    canvas.off('mouse:down');
    canvas.off('mouse:move');
    canvas.off('mouse:up');
    canvas.forEachObject(function(o){ o.setCoords() });
    $(".editor-tools-item").click(function(){
        $('.editor-tools-item').removeClass('tool-active');
        $(this).addClass('tool-active');
        var tool = $(this).attr('data');
        console.log("tool :" + tool);
        addObj(tool);
    });
    $(".editor-full-screen-ico").click(function () {
        if( $(".reklamshop-editor-cover").hasClass("editor-fullScreen")) {
            $(".reklamshop-editor-cover").removeClass("editor-fullScreen");
        } else {
            $(".reklamshop-editor-cover").addClass("editor-fullScreen");
        }
    });
    var circle, isDown, origX, origY;

    canvas.observe('mouse:down', function(o){
        isDown = true;
        var pointer = canvas.getPointer(o.e);
        origX = pointer.x;
        origY = pointer.y;
        if(!o.target) {

            if (obj == 'circle') {
                circle = new fabric.Circle({
                    left: pointer.x,
                    top: pointer.y,
                    radius: 1,
                    strokeWidth: 1,
                    stroke: 'black',
                    fill: 'red',
                    selectable: true,
                    originX: 'center', originY: 'center',
                    uuid : generateUUID()
                });
                canvas.add(circle);

            }
            if (obj == 'rect') {

                 rect = new fabric.Rect({
                    left: pointer.x,
                    top: pointer.y,
                    width: pointer.x - origX,
                    height: pointer.y - origY,
                    fill: '',
                    stroke: 'red',
                    type: 'rect',
                    uuid : generateUUID(),
                    strokeWidth: 5,
                    selectable: true,
                    hasControls: true
                });
                magic.editor.add(rect);

            }
            if(obj == 'line') {
                var points = [pointer.x, pointer.y, pointer.x, pointer.y];
                line = new fabric.Line(points, {
                    strokeWidth: 6,
                    fill: 'red',
                    stroke: 'red',
                    originX: 'center',
                    originY: 'center',
                    id:'arrow_line',
                    uuid : generateUUID(),
                    type : 'arrow'
                });
               canvas.add(line);
            }
            if(obj == 'text') {
                text = new fabric.CurvesText('foo', {
                //text = new fabric.Textbox('foo', {
                    fontFamily: 'Caveat',
                    //fontFamily: 'Delicious_500',
                    left: pointer.x,
                    top: pointer.y,
                    objecttype: 'text'
                });
                text.setControlsVisibility({
                    mt: false, // middle top disable
                    mb: false, // midle bottom

                });
                canvas.add(text);

                canvas.renderAll();
            }
        } else {
            if(obj == 'remove') {
                deleteObjects();
            }
        }
    });

    canvas.observe('mouse:move', function(o){
        if (!isDown) return;
        if(o.target) return;
        var pointer = canvas.getPointer(o.e);
        if(obj == 'circle') {
            circle.set({radius: Math.abs(origX - pointer.x)});
        }
        if(obj=='rect') {
            rect.set({ width: Math.abs(origX - pointer.x) });
            rect.set({ height: Math.abs(origY - pointer.y) });
        }
        if(obj == 'line') {
            line.set({
                x2: pointer.x,
                y2: pointer.y
            });
        }
        if(obj == 'text') {
            text.set({ width: Math.abs(origX - pointer.x) });
            text.set({ height: Math.abs(origY - pointer.y) });
        }
         canvas.renderAll();
    });
    let activeObject;
    canvas.on('mouse:up', function(o){
        isDown = false;
        var objs = canvas.getObjects();
        for (var i = 0 ; i < objs.length; i++) {
            objs[i].setCoords();
        }
        activeObject = canvas.getActiveObject();
        if (activeObject) {
            positionControlPanel(activeObject);
            activeObject.on('moving', function() { positionControlPanel(activeObject) });
            activeObject.on('scaling', function() { positionControlPanel(activeObject) });
            activeObject.on('mouse:up', function() { positionControlPanel(activeObject) });
        } else {
            document.getElementById('editor-control-panel').style.display="none";
        }

    });
    function generateUUID(){
        var d = new Date().getTime();
        if(window.performance && typeof window.performance.now === "function"){
            d += performance.now(); //use high-precision timer if available
        }
        var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = (d + Math.random()*16)%16 | 0;
            d = Math.floor(d/16);
            return (c=='x' ? r : (r&0x3|0x8)).toString(16);
        });
        return uuid;
    }
    function deleteObjects(){
        let activeObject = canvas.getActiveObjects();
        if (activeObject) {
            if (confirm('Are you sure?')) {
                let objectsInGroup = activeObject;
                canvas.discardActiveObject();
                objectsInGroup.forEach(function(object) {
                        canvas.remove(object);
                });
            } else {
                return;
            }
        }
    }
    // Zoom into
    canvas.on('mouse:wheel', function(opt) {
        if(obj=='zoom') {
           setZoom('wheel',opt);
        }
    });
    canvas.on('mouse:down', function(opt) {
        var evt = opt.e;
        if(obj==='zoom') {
            if (evt.altKey === true) {
                this.isDragging = true;
                this.selection = false;
                this.lastPosX = evt.clientX;
                this.lastPosY = evt.clientY;
            }
        }
        if(obj === 'drag' ) {
            this.isDragging = true;
            this.selection = false;
            this.lastPosX = evt.clientX;
            this.lastPosY = evt.clientY;
        }
    });
    canvas.on('mouse:move', function(opt) {
        if(obj === 'zoom' || obj === 'drag') {
            if (this.isDragging) {
                drag(opt);
                console.log('in dragginbg');
            }
        }
    });
    canvas.on('mouse:up', function(opt) {
        if(obj === 'zoom' || obj === 'drag') {
            this.isDragging = false;
            this.selection = true;
        }
    });
    function drag(opt) {
        var e = opt.e;
       // var vpt = this.viewportTransform;
       var vpt = magic.editor.viewportTransform;
        vpt[4] += e.clientX - magic.editor.lastPosX;
        vpt[5] += e.clientY - magic.editor.lastPosY;
        magic.editor.requestRenderAll();
        magic.editor.lastPosX = e.clientX;
        magic.editor.lastPosY = e.clientY;

    }
    //zoom bar
    let zoomBar = document.querySelector('#zoom-bar');
    zoomBar.addEventListener('input', function () {
       console.log("zoom.val " + zoomBar.value );
       setZoom('zoom-bar',zoomBar.value);
    }, false);
    function setZoom(typ,val) {
        if(typ == 'wheel') {
            var opt = val;
            var delta = opt.e.deltaY;
            var zoom = canvas.getZoom();
            zoom *= 0.999 ** delta;
            if (zoom > 10) zoom = 10;
            if (zoom < 0.1) zoom = 0.1;
            canvas.setZoom(zoom);
            console.log(zoom);
            opt.e.preventDefault();
            opt.e.stopPropagation();
            document.getElementById('zoom-bar').step=zoom;
            //i.step = zoom;
        }
        if(typ == 'zoom-bar') {
            canvas.setZoom(val);
        }
    }
    //ecp-color-control
    let colorControl = document.querySelector('#ecp-color-control');
    colorControl.addEventListener('input',function () {
        activeObject.set('fill',colorControl.value);
        canvas.renderAll();
    });
    //ecp-font-size
    let setFontSize = document.querySelector('#ecp-font-size');
    setFontSize.addEventListener('input',function () {
        activeObject.set('fontSize',setFontSize.value);
        canvas.renderAll();
    });
    //ecp-font-bold
    let setFontBold = document.querySelector('#ecp-font-bold');
    setFontBold.addEventListener('click', function () {
        if(activeObject.fontWeight === 'normal') {
            activeObject.set('fontWeight','bold');
        } else {
            activeObject.set('fontWeight','normal');
        }
        canvas.renderAll();
    });
    //ecp-font-italic
    let setFontItalic = document.querySelector('#ecp-font-italic');
    setFontItalic.addEventListener('click', function () {
        if(activeObject.fontStyle === 'normal') {
            activeObject.set('fontStyle','italic');
        } else {
            activeObject.set('fontStyle','normal');
        }
        canvas.renderAll();
    });
    //ecp-text-align-left
    let setTextAlignLeft = document.querySelector('#ecp-text-align-left');
    setTextAlignLeft.addEventListener('click',function () {
        activeObject.set('textAlign','left');
        canvas.renderAll();
    });
    //ecp-text-align-center
    let setTextAlignCenter = document.querySelector('#ecp-text-align-center');
    setTextAlignCenter.addEventListener('click',function () {
        activeObject.set('textAlign','center');
        canvas.renderAll();
    });
    //ecp-text-align-center
    let setTextAlignRight = document.querySelector('#ecp-text-align-right');
    setTextAlignRight.addEventListener('click',function () {
        activeObject.set('textAlign','right');
        canvas.renderAll();
    });
    //ecp-arrange-flip-front
    let setArrangeFlipFront =document.querySelector('#ecp-arrange-flip-front');
    setArrangeFlipFront.addEventListener('click',function () {
        var activeObj = canvas.getActiveObject();
        canvas.bringToFront(activeObj).discardActiveObject(activeObj).renderAll();
    });
    //ecp-arrange-flip-back
    let setArrangeFlipBack =document.querySelector('#ecp-arrange-flip-back');
    setArrangeFlipBack.addEventListener('click',function () {
        var activeObj = canvas.getActiveObject();
        canvas.sendToBack(activeObj).discardActiveObject(activeObj).renderAll();
    });
    //ecp-arrange-send-backward
    let setArrangeBackward =document.querySelector('#ecp-arrange-send-backward');
    setArrangeBackward.addEventListener('click',function () {
        var activeObj = canvas.getActiveObject();
        canvas.sendBackwards(activeObj).discardActiveObject(activeObj).renderAll();
    });
    //ecp-arrange-bring-forward
    let setArrangeBringForward =document.querySelector('#ecp-arrange-bring-forward');
    setArrangeBringForward.addEventListener('click',function () {
        var activeObj = canvas.getActiveObject();
        canvas.bringForward(activeObj).discardActiveObject(activeObj).renderAll();
    });
    var fonts = [
        'Notable',
        'Piedra',
        'MuseoModerno',
        'Pangolin'
    ];
    fonts.unshift('Times New Roman');
    var select = document.getElementById("ecp-font-family");
    fonts.forEach(function(font) {
        var option = document.createElement('option');
        option.innerHTML = font;
        option.value = font;
        select.appendChild(option);
    });
    // Apply selected font on change
    document.getElementById('ecp-font-family').onchange = function() {
        if (this.value !== 'Times New Roman') {
            loadAndUse(this.value);
        } else {

            canvas.getActiveObject().set("fontFamily", this.value);
            canvas.requestRenderAll();
        }
    };

    function loadAndUse(font) {
        var myfont = new FontFaceObserver(font)
        myfont.load()
            .then(function() {
                // when font is loaded, use it.
                console.log(canvas.getActiveObject());
                canvas.getActiveObject().set("fontFamily", font);

                canvas.requestRenderAll();
            }).catch(function(e) {
           console.log(e)
           // alert('font loading failed ' + font);
        });
    }

    //font curvers
    var  font_path = php_vars.plugins_url +'Caveat.ttf';
    opentype.load(font_path, function (err, font) {
        if (err) {
            console.error('Error loafontsArrding font ', err);
            return
        }
        fontsArr['Caveat'] = {
            obj: font,
            name: 'Caveat',
        }
    });
    //font Notable
    var  font_path = php_vars.plugins_url +'Notable-Regular.ttf';
    opentype.load(font_path, function (err, font) {
        if (err) {
            console.error('Error loafontsArrding font ', err);
            return
        }
        fontsArr['Notable'] = {
            obj: font,
            name: 'Notable',
        }
    });
    // Move object within canvas boundary limit
    canvas.on('object:moving', function (e) {
        var obj = e.target;
        // if object is too big ignore
        if(obj.currentHeight > obj.canvas.height || obj.currentWidth > obj.canvas.width){
            return;
        }
        obj.setCoords();
        // top-left  corner
        if(obj.getBoundingRect().top < 0 || obj.getBoundingRect().left < 0){
            obj.top = Math.max(obj.top, obj.top-obj.getBoundingRect().top);
            obj.left = Math.max(obj.left, obj.left-obj.getBoundingRect().left);
        }
        // bot-right corner
        if(obj.getBoundingRect().top+obj.getBoundingRect().height  > obj.canvas.height || obj.getBoundingRect().left+obj.getBoundingRect().width  > obj.canvas.width){
            obj.top = Math.min(obj.top, obj.canvas.height-obj.getBoundingRect().height+obj.top-obj.getBoundingRect().top);
            obj.left = Math.min(obj.left, obj.canvas.width-obj.getBoundingRect().width+obj.left-obj.getBoundingRect().left);
        }
    });
    // Resize object within canvas boundary limit
    canvas.on('object:scaling', function (e) {
        var shape = e.target;
        if ( shape.maxScale != undefined ) {
            maxScale = shape.maxScale;
            if ( !isNaN( maxScale ) && shape.scaleX >= maxScale ) {
                shape.set( { scaleX: maxScale } )
            }
            if ( !isNaN( maxScale ) && shape.scaleY >= maxScale ) {
                shape.set( { scaleY: maxScale } )
            }
        }
    });
    $("#upload-menu-button").click(function () {
        if (! $(".upload-buttons-container").hasClass('inShow')) {
            $(".upload-buttons-container").addClass("inShow");
        } else {
            $(".upload-buttons-container").removeClass("inShow");
        }

    });
    /*
    $(".font-button").click(function () {
        $('.add-ons-cover').addClass("in-display");

    });
    */

    $(".add-ons-close").click(function () {
        clearAddOnsContainer();
    });
    $('.add-on-button').click(function () {
       openAddOnsContainer($(this).attr('data'));
    });
    const openAddOnsContainer = function (obj) {
        clearAddOnsContainer();
        switch (obj) {
            case 'font-button':
                $('.add-ons-cover').addClass("in-display");
                $('.fonts-container').addClass("in-display");
                break;
            case 'shapes-button':
                $('.add-ons-cover').addClass("in-display");
                $('.shapes-container').addClass("in-display");
                break;
            case 'upload-button' :
                $('.add-ons-cover').addClass("in-display");
                $('.upload-container').addClass("in-display");
                break;
        }
    };
    const clearAddOnsContainer = function () {
        $('.add-ons-cover').removeClass("in-display");
        $('.add-ons-container').removeClass("in-display");
        $('.add-on-button').removeClass('in-display');
    };
});

//End of jQuery


var controlPanel = document.getElementById('editor-control-panel');

function positionControlPanel(obj) {
    var absCoords = magic.editor.getAbsoluteCoords(obj);
    //console.log('moving');
    controlPanel.style.position="absolute";
    controlPanel.style.zIndex=9;
    controlPanel.style.display="block";
    controlPanel.style.left = (obj.aCoords.tr.x + 10 ) + 'px';
    controlPanel.style.top = (absCoords.top ) + 'px';
   // console.log(obj.aCoords);
   // console.log(absCoords);
}


fabric.Image.fromURL('https://upload.wikimedia.org/wikipedia/en/4/46/IMG_Academy_Logo.jpg', function(img) {

    magic.editor.add(img.set({ left: 250, top: 250 }).scale(0.25));

});


function do_save() {
    var filedata=magic.editor.toSVG(); // the SVG file is now in filedata

    var locfile = new Blob([filedata], {type: "image/svg+xml;charset=utf-8"});
    var locfilesrc = URL.createObjectURL(locfile);//mylocfile);

    var dwn = document.getElementById('dwn');
    dwn.innerHTML = "<a href=" + locfilesrc + " download='mysvg.svg'>Download</a>";
}

function toDataURL(src, callback, outputFormat) {
    var img = new Image();
    img.crossOrigin = 'Anonymous';
    img.onload = function() {
        var canvas = document.createElement('CANVAS');
        var ctx = canvas.getContext('2d');
        var dataURL;
        canvas.height = this.naturalHeight;
        canvas.width = this.naturalWidth;
        ctx.drawImage(this, 0, 0);
        dataURL = canvas.toDataURL(outputFormat);
        callback(dataURL);
    };
    img.src = src;
    if (img.complete || img.complete === undefined) {
        //img.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
        img.src = src;
    }
}


//const input = document.getElementById('myfile');
//input.addEventListener('change', updateImageDisplay);
//input.addEventListener('change', readFile);

function updateImageDisplay(event) {
    console.log(input.value);
    //'https://www.gravatar.com/avatar/d50c83cc0c6523b4d3f6085295c953e0'
    toDataURL(
        event.target.result,
        function(dataUrl) {
            console.log('RESULT:', dataUrl);

        }
    )
}
/*
function readFile() {

    if (this.files && this.files[0]) {

        var FR= new FileReader();

        FR.addEventListener("load", function(e) {
           // document.getElementById("img").src       = e.target.result;
           // document.getElementById("b64").innerHTML = e.target.result;
            toDataURL(
                e.target.result,
                function(dataUrl) {

                    fabric.Image.fromURL(dataUrl, function(img) {
                        magic.editor.add(img.set({ left: 50, top: 50 }).scale(0.50));
                    });
                    magic.editor.renderAll();
                }
            )
        });

        FR.readAsDataURL( this.files[0] );
    }

}
*/

Dropzone.autoDiscover = false;

var myDropzone = new Dropzone(".dropzone");

myDropzone.on("queuecomplete", function(file, res) {
    if (myDropzone.files[0].status == Dropzone.SUCCESS) {
        console.log(myDropzone.files[0].dataURL);
        fabric.Image.fromURL(myDropzone.files[0].dataURL, function(img) {
            magic.editor.add(img.set({ left: 50, top: 50 }).scale(0.50));
        });
        magic.editor.renderAll();
        var element = document.getElementById("upload-buttons-container");
        element.classList.remove("inShow");
        var modal = document.getElementById("upload-modal");
        modal.style.display="none";
    }
});
