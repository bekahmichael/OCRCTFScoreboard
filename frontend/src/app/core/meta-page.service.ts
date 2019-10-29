import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { Title } from '@angular/platform-browser';

@Injectable()
export class MetaPageService {
    constructor (
        private title: Title
    ) {
    }

    setTitle (name) {
        this.title.setTitle(environment.APP_NAME + ' | ' + name);
    }
}
