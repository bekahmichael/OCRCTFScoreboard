import { Component, Input, AfterViewInit, ElementRef, ViewChild} from '@angular/core';
import { IWoDialogComponent } from '../wo-dialog/wo-dialog.interface';
@Component({
    template: `
        <div class="wo-dialog-alert__body" (mousemove)="doCropMove($event)"
            [class.nw-cursor]="crop.nwMove === true"
            [class.ne-cursor]="crop.neMove === true"
            [class.se-cursor]="crop.seMove === true"
            [class.sw-cursor]="crop.swMove === true"
        >
            <div class="awatar-edit__title">Crop Image</div>
            <div class="awatar-edit__wrapper">
                <div class="awatar-edit" [style.height]="param.wrapHeight + 'px'" [style.width]="param.wrapWidht + 'px'" #containerEl>
                    <img
                        [width]="param.width"
                        [height]="param.height"
                        [style.left]="param.left + 'px'"
                        [style.top]="param.top + 'px'"
                    #imageEl>
                    <div class="cropper-wrap-box"
                        [style.width]="param.width === null ? 0 : param.width + 'px'"
                        [style.height]="param.height === null ? 0 : param.height + 'px'"
                        [style.left]="param.left + 'px'"
                        [style.top]="param.top + 'px'"
                    >
                        <div class="cropper-bg"
                            style="top: 0; left: 0; right: 0;"
                            [style.height]="crop.top + 'px'"
                        ></div>
                        <div class="cropper-bg"
                            style="left: 0"
                            [style.width]="crop.left + 'px'"
                            [style.height]="crop.height + 'px'"
                            [style.top]="crop.top + 'px'"
                        ></div>
                        <div class="cropper-bg"
                            style="right: 0"
                            [style.width]="(param.width - crop.left - crop.width) + 'px'"
                            [style.height]="crop.height + 'px'"
                            [style.top]="crop.top + 'px'"
                        ></div>
                        <div class="cropper-bg"
                            style="bottom: 0; left: 0; right: 0;"
                            [style.height]="(param.height - crop.top - crop.height) + 'px'"
                        ></div>

                        <div class="cropper"
                            (mousedown)="doCropMoveStart($event, 'drugMove')"
                            [style.width]="crop.width + 'px'"
                            [style.height]="crop.height + 'px'"
                            [style.top]="crop.top + 'px'"
                            [style.left]="crop.left + 'px'"
                        ></div>

                        <div class="nh-st" style="cursor: nw-resize;"
                            [style.top]="(crop.top - 10) + 'px'"
                            [style.left]="(crop.left - 10) + 'px'"
                            (mousedown)="doCropMoveStart($event, 'nwMove')"
                        >
                            <div class="nh-tt"></div>
                        </div>
                        <div class="nh-st" style="cursor: ne-resize;"
                            [style.top]="(crop.top - 10) + 'px'"
                            [style.left]="(crop.left + crop.width - 10) + 'px'"
                            (mousedown)="doCropMoveStart($event, 'neMove')"
                        >
                            <div class="nh-tt"></div>
                        </div>
                        <div class="nh-st"  style="cursor: se-resize;"
                            [style.top]="(crop.top + crop.height - 10) + 'px'"
                            [style.left]="(crop.left + crop.width - 10) + 'px'"
                            (mousedown)="doCropMoveStart($event, 'seMove')"
                        >
                            <div class="nh-tt"></div>
                        </div>
                        <div class="nh-st" style="cursor: sw-resize;"
                            [style.top]="(crop.top + crop.height - 10) + 'px'"
                            [style.left]="(crop.left - 10) + 'px'"
                            (mousedown)="doCropMoveStart($event, 'swMove')"
                        >
                            <div class="nh-tt"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="wo-dialog-alert__buttons">
            <a class="btn btn-primary" href="javascript:void(0);" (click)="confirm()">Confirm</a>
            <a class="btn btn-default" href="javascript:void(0);" (click)="close()">Close</a>
        </div>
    `,
    styles: [`
        :host * {
            box-sizing: initial;
        }
        .nw-cursor {
            cursor: nw-resize !important;
        }
        .sw-cursor {
            cursor: sw-resize !important;
        }
        .ne-cursor {
            cursor: ne-resize !important;
        }
        .se-cursor {
            cursor: se-resize !important;
        }
        .wo-dialog-alert__body {
            padding: 10px 15px 15px;
        }
        .wo-dialog-alert__buttons {
            padding: 8px 10px 8px;
            text-align: right;
            border-top: 1px solid #eaeaea;
        }
        .wo-dialog-alert__buttons a {
            min-width: 50px;
            margin-left: 5px;
        }
        .awatar-edit__wrapper {
            padding: 20px;
            overflow: hidden;
            user-select: none;
        }
        .awatar-edit {
            position: relative;
        }
        .awatar-edit img {
            opacity: 0;
            position: absolute;
            user-select: none;
        }
        .cropper-wrap-box {
            position: absolute;
            border: 1px solid #fff;
        }
        .cropper {
            box-shadow: inset 1px 1px 0 rgba(0,0,0,0.1), inset 0 -1px 0 rgba(0,0,0,0.07);
            border: 1px solid rgba(0,0,0,0.6);
            cursor: move;
            position: absolute;
            box-sizing: border-box;
        }
        .cropper-bg {
            z-index: 10;
            position: absolute;
            background-color: #fff;
            opacity: .5;
        }
        .nh-st {
            position: absolute;
            padding: 10px;
            z-index: 10;
        }
        .nh-tt {
            position: absolute;
            background-color: #fff;
            border: 1px solid #000;
            height: 8px;
            overflow: hidden;
            width: 8px;
            top: 5px;
            left: 5px;
        }
        .awatar-edit__title {
            font-size: 16px;
            font-weight: bold;
            padding-bottom: 10px;
        }
    `],
})
export class WoAwatarEditComponent implements AfterViewInit, IWoDialogComponent {
    @Input() dialog: any;
    @ViewChild('imageEl', {static: false}) imageEl: ElementRef;
    @ViewChild('containerEl', {static: false}) containerEl: ElementRef;
    file = '';

    param = {
        wrapWidht: 700,
        wrapHeight: 350,
        width: null,
        height: null,
        left: 0,
        top: 0,
    };

    crop = {
        minWidth: 50,
        minHeight: 50,
        width: 200,
        height: 200,
        left: 0,
        top: 0,
        drugMove: false,
        nwMove: false,
        neMove: false,
        seMove: false,
        swMove: false,
        startX: 0,
        startY: 0,
        startTop: 0,
        startLeft: 0,
        startWidth: 0,
        startHeight: 0,
    };

    ngAfterViewInit () {
        this.dialog.options.css = {
            width: (this.param.wrapWidht + 70) + 'px',
        };

        if (this.dialog.options && this.dialog.options.file) {
            const reader  = new FileReader();

            reader.onload = () => {
                this.imageEl.nativeElement.onload = () => {
                    this.initElements();
                };
                this.imageEl.nativeElement.src = reader.result;
            };

            reader.readAsDataURL(this.dialog.options.file);
        }

        document.body.addEventListener('mouseup', (event) => {
            this.doCropMoveStop(event);
        });
    }

    initElements() {
        this.imageEl.nativeElement.style.opacity = 1;

        this.param.height = this.param.wrapHeight;
        this.param.width = (this.param.height / this.imageEl.nativeElement.naturalHeight) * this.imageEl.nativeElement.naturalWidth;

        if (this.param.width > this.param.wrapWidht) {
            this.param.width = this.param.wrapWidht;
            this.param.height = (this.param.width / this.imageEl.nativeElement.naturalWidth) * this.imageEl.nativeElement.naturalHeight;
        }

        this.crop.width = this.crop.height = Math.min(this.crop.width, this.param.width, this.param.height);

        this.toCenter();
    }

    toCenter() {
        this.param.left = (this.param.wrapWidht - this.param.width) / 2;
        this.param.top = (this.param.wrapHeight - this.param.height) / 2;
        this.crop.left = (this.param.width - this.crop.width) / 2;
        this.crop.top = (this.param.height - this.crop.height) / 2;
    }

    doCropMoveStart(event: MouseEvent, key) {
        this.crop.startX = event.clientX;
        this.crop.startY = event.clientY;
        this.crop.startTop = this.crop.top;
        this.crop.startLeft = this.crop.left;
        this.crop.startWidth = this.crop.width;
        this.crop.startHeight = this.crop.height;
        this.crop[key] = true;
    }

    doCropMove(event: MouseEvent) {
        if (this.crop.drugMove === true) {
            this.crop.top = this.crop.startTop - (this.crop.startY - event.clientY);
            this.crop.left = this.crop.startLeft - (this.crop.startX - event.clientX);

            if (this.crop.top < 0) {
                this.crop.top = 0;
            } else if (this.crop.top > this.param.height - this.crop.height) {
                this.crop.top = this.param.height - this.crop.height;
            }
            if (this.crop.left < 0) {
                this.crop.left = 0;
            } else if (this.crop.left > this.param.width - this.crop.width) {
                this.crop.left = this.param.width - this.crop.width;
            }
        } else if (this.crop.nwMove === true) {
            this.moveNwLimit(event);
        } else if (this.crop.swMove === true) {
            this.moveSwLimit(event);
        } else if (this.crop.neMove === true) {
            this.moveNeLimit(event);
        } else if (this.crop.seMove === true) {
            this.moveSeLimit(event);
        }
    }

    // top-left
    moveNwLimit(event) {
        this.crop.width = this.crop.startWidth + (this.crop.startX - event.clientX);
        this.crop.height = this.crop.startHeight + (this.crop.startY - event.clientY);
        if (this.crop.height >= this.crop.minHeight) {
            this.crop.top = this.crop.startTop - (this.crop.startY - event.clientY);
        }
        if (this.crop.width >= this.crop.minWidth) {
            this.crop.left = this.crop.startLeft - (this.crop.startX - event.clientX);
        }

        if (this.crop.width >= this.crop.minWidth) {
            if (this.crop.width > this.crop.startLeft + this.crop.startWidth) {
                this.crop.width = this.crop.startLeft + this.crop.startWidth;
                this.crop.left = 0;
            } else {
                this.crop.left = this.crop.startLeft - (this.crop.startX - event.clientX);
            }
        } else {
            this.crop.left = this.crop.startLeft + this.crop.startWidth - this.crop.minWidth;
        }

        if (this.crop.top < 0) {
            this.crop.top = 0;
            this.crop.height = this.crop.startTop + this.crop.startHeight;
        }

        if (this.crop.width < this.crop.minWidth) {
            this.crop.width = this.crop.minWidth;
        }
        if (this.crop.height < this.crop.minHeight) {
            this.crop.height = this.crop.minWidth;
            this.crop.top = this.crop.startTop + this.crop.startHeight - this.crop.minHeight;
        }

        if (this.crop.width > this.crop.height) {
            this.crop.left = this.crop.startLeft + this.crop.startWidth - this.crop.height;
            this.crop.width = this.crop.startLeft + this.crop.startWidth - this.crop.left;
        } else {
            this.crop.height = this.crop.width;
            this.crop.top = this.crop.startTop + this.crop.startHeight - this.crop.height;
        }
    }

    // bottom-right
    moveSeLimit(event) {
        this.crop.width = this.crop.startWidth - (this.crop.startX - event.clientX);
        this.crop.height = this.crop.startHeight - (this.crop.startY - event.clientY);

        if (this.crop.left + this.crop.width > this.param.width) {
            this.crop.width = this.param.width - this.crop.left;
        }
        if (this.crop.top + this.crop.height > this.param.height) {
            this.crop.height = this.param.height - this.crop.top;
        }

        if (this.crop.width < this.crop.minWidth) {
            this.crop.width = this.crop.minWidth;
        }
        if (this.crop.height < this.crop.minHeight) {
            this.crop.height = this.crop.minWidth;
        }

        this.crop.width = Math.min(this.crop.width, this.crop.height);
        this.crop.height = Math.min(this.crop.width, this.crop.height);
    }

    // top-right
    moveNeLimit(event) {
        this.crop.width = this.crop.startWidth - (this.crop.startX - event.clientX);
        this.crop.height = this.crop.startHeight + (this.crop.startY - event.clientY);

        if (this.crop.height >= this.crop.minHeight) {
            this.crop.top = this.crop.startTop - (this.crop.startY - event.clientY);
        } else {
            this.crop.top = this.crop.startTop + this.crop.startHeight - this.crop.minHeight;
        }

        if (this.crop.left + this.crop.width > this.param.width) {
            this.crop.width = this.param.width - this.crop.left;
        }

        if (this.crop.width < this.crop.minWidth) {
            this.crop.width = this.crop.minWidth;
        }
        if (this.crop.height < this.crop.minHeight) {
            this.crop.height = this.crop.minWidth;
        }

        if (this.crop.top < 0) {
            this.crop.top = 0;
            this.crop.height = this.crop.startTop + this.crop.startHeight;
        }

        if (this.crop.height < this.crop.width) {
            this.crop.width = this.crop.height;
        }

        if (this.crop.height > this.crop.width) {
            this.crop.height = this.crop.width;
            this.crop.top = this.crop.startTop + this.crop.startHeight - this.crop.height;
        }
    }

    // bottom-left
    moveSwLimit(event) {
        this.crop.height = this.crop.startHeight - (this.crop.startY - event.clientY);
        this.crop.width = this.crop.startWidth + (this.crop.startX - event.clientX);

        if (this.crop.height > this.param.height - this.crop.top) {
            this.crop.height = this.param.height - this.crop.top;
        }

        if (this.crop.width >= this.crop.minWidth) {
            if (this.crop.width > this.crop.startLeft + this.crop.startWidth) {
                this.crop.width = this.crop.startLeft + this.crop.startWidth;
                this.crop.left = 0;
            } else {
                this.crop.left = this.crop.startLeft - (this.crop.startX - event.clientX);
            }
        } else {
            this.crop.left = this.crop.startLeft + this.crop.startWidth - this.crop.minWidth;
        }

        if (this.crop.width < this.crop.minWidth) {
            this.crop.width = this.crop.minWidth;
        }
        if (this.crop.height < this.crop.minHeight) {
            this.crop.height = this.crop.minWidth;
        }

        if (this.crop.width < this.crop.height) {
            this.crop.height = this.crop.width;
        }

        if (this.crop.width > this.crop.height) {
            this.crop.left = this.crop.startLeft + this.crop.startWidth - this.crop.height;
            this.crop.width = this.crop.startLeft + this.crop.startWidth - this.crop.left;
        }
    }

    doCropMoveStop(event: MouseEvent) {
        this.crop.startX = event.clientX;
        this.crop.startY = event.clientY;
        this.crop.drugMove = false;
        this.crop.nwMove = false;
        this.crop.neMove = false;
        this.crop.seMove = false;
        this.crop.swMove = false;
    }

    confirm() {
        const k = this.imageEl.nativeElement.naturalHeight / this.imageEl.nativeElement.height;
        this.dialog.close({
            confirm: true,
            crop: {
                naturalHeight: this.imageEl.nativeElement.naturalHeight,
                naturalWidth: this.imageEl.nativeElement.naturalWidth,
                left: this.crop.left * k,
                top: this.crop.top * k,
                width: this.crop.width * k,
                height: this.crop.height * k,
            },
        });
    }

    close() {
        this.dialog.close({
            confirm: false,
        });
    }
}
