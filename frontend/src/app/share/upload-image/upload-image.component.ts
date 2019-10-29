import { Component, ElementRef, ViewChild, AfterViewInit, Input, EventEmitter, Output } from '@angular/core';
import { FileUploadService } from '@app/core/file-upload-service';

import { WoDialogService } from '@app/wo-module/wo-dialog/wo-dialog.service';
import {WoDialogConfirmComponent} from '@app/wo-module/wo-dialog/wo-dialog-confirm/wo-dialog-confirm.component';
import {ApiAdminService} from '@app/share/api-admin.service';

@Component({
    selector: 'upload-image',
    templateUrl: 'upload-image.component.html',
    styleUrls: ['upload-image.component.css'],
})
export class UploadImageComponent implements AfterViewInit {
    @ViewChild('inputFile', {static: false}) inputFile: ElementRef;
    @Input() woSrc: string;
    @Input() woFullSrc: string;
    @Input() woId: string;
    @Output() changeImage = new EventEmitter();

    isLoading = false;

    constructor (
        private fileUploadService: FileUploadService,
        public api: ApiAdminService,
        private dialog: WoDialogService
    ) {
    }

    ngAfterViewInit() {
        this.inputFile.nativeElement.onchange = () => {
            this.doUploadFile();
            this.resetInputField();
        };
    }

    resetInputField() {
        this.inputFile.nativeElement.type = 'text';
        this.inputFile.nativeElement.type = 'file';
    }

    doUploadFile() {
        this.isLoading = true;
        const url = new URL(this.woSrc, window.location.origin);
        url.searchParams.set('id',  (this.woId ? this.woId : ''));

        this.fileUploadService.upload(url.href, this.inputFile.nativeElement.files).then(res => {
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
        const dialogRef = this.dialog.open(WoDialogConfirmComponent, {message: 'Are you sure you want to delete image?'});
        dialogRef.afterClosed().subscribe(result => {
            if (result === true) {
                this.api.send('files/image-delete', {id: this.woId}, {}).then(res => {
                    this.changeImage.emit({result: null, remove: true});
                });
            }
        });
    }

    getAvatarUrl() {
        const url = new URL(this.woSrc, window.location.origin);
        url.searchParams.set('id',  (this.woId ? this.woId : ''));
        return url.href;
    }

    getFullSrc() {
        if (typeof this.woFullSrc === 'undefined' || this.woFullSrc === null) {
            return null;
        } else {
            const url = new URL(this.woFullSrc, window.location.origin);
            url.searchParams.set('id',  (this.woId ? this.woId : ''));
            return url.href;
        }
    }
}
