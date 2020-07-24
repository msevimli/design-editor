class magicEditor {
    constructor(editorId,areaId) {
        this.editor = new fabric.Canvas(editorId);
        this.editorArea = areaId;
        fabric.Object.prototype.transparentCorners = false;
        fabric.Object.prototype.originX = fabric.Object.prototype.originX = 'left';
        fabric.Canvas.prototype.getAbsoluteCoords = function(object) {
            return {
                //left: object.left + this._offset.left,
                left: object.left ,
                //top: object.top + this._offset.top
                top: object.top
            };
        }
    }
     resizeIt(width,height) {
         document.getElementById(this.editorArea).style.width = width + "px";
         this.editor.setWidth(width);
         this.editor.setHeight(height);
    }
     bring_front() {
        var myObject = this.editor.getActiveObject();
        // canvas.sendBackwards(myObject)
       //  canvas.sendToBack(myObject)
        this.editor.bringForward(myObject)
        this.editor.bringToFront(myObject)
    }
}
