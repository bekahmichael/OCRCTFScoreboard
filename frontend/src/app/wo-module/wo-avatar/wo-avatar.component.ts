import { Component, ElementRef, ViewChild, AfterViewInit, Input, EventEmitter, Output } from '@angular/core';
import { FileUploadService } from '@app/core/file-upload-service';

import { WoDialogService } from '@app/wo-module/wo-dialog/wo-dialog.service';
import { WoAwatarEditComponent } from './wo-awatar-edit.component';

@Component({
    selector: 'wo-avatar',
    templateUrl: 'wo-avatar.component.html',
    styleUrls: ['wo-avatar.component.css'],
})
export class WoAvatarComponent implements AfterViewInit {
    @ViewChild('inputFile', {static: false}) inputFile: ElementRef;
    @Input() woSrc: string;
    @Input() woId: string;
    @Output() changeImage = new EventEmitter();

    isLoading = false;
    crop: {};

    constructor (
        private fileUploadService: FileUploadService,
        private dialog: WoDialogService
    ) {
    }

    ngAfterViewInit() {
        this.inputFile.nativeElement.onchange = () => {
            const dialogRef = this.dialog.open(WoAwatarEditComponent, {file: this.inputFile.nativeElement.files[0]});
            dialogRef.afterClosed().subscribe(result => {
                if (result !== null && result['confirm'] === true) {
                    this.crop = result['crop'];
                    this.doUploadFile();
                }
                this.resetInputField();
            });
        };
    }

    resetInputField() {
        this.inputFile.nativeElement.type = 'text';
        this.inputFile.nativeElement.type = 'file';
    }

    doUploadFile() {
        this.isLoading = true;
        const url = this.woSrc + '/?id=' + (this.woId ? this.woId : '');
        this.fileUploadService.upload(url, this.inputFile.nativeElement.files, {crop: this.crop}).then(res => {
            this.resetInputField();
            this.isLoading = false;
            this.changeImage.emit({result: res});
        }).catch(err => {
            this.resetInputField();
            this.isLoading = false;
            this.changeImage.emit({result: null, error: err});
        });
    }

    doRemove() {
        this.changeImage.emit({result: null, remove: true});
    }

    getAvatarUrl() {
        return this.woSrc + '/?id=' + (this.woId ? this.woId : '');
    }
}
