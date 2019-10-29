import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpService } from './http.service';
import { MetaPageService } from './meta-page.service';
import { AppStorageService } from './app-storage.service';
import { FileUploadService } from './file-upload-service';

@NgModule({
    imports: [
        CommonModule
    ],
    declarations: [],
    providers: [
        HttpService,
        MetaPageService,
        AppStorageService,
        FileUploadService,
    ],
})
export class CoreModule { }
