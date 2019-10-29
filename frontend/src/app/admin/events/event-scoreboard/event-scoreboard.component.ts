import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { ApiAdminService } from '@app/share/api-admin.service';
import { WoFlashService } from '@app/wo-module/wo-flash/wo-flash.service';
import { MetaPageService } from '@app/core/meta-page.service';

@Component({
    templateUrl: 'event-scoreboard.component.html',
    styleUrls: ['event-scoreboard.component.css']
})
export class EventScoreboardComponent implements OnInit {
    eventId;
    scoreboard = {};
    is_downclock = '1';
    dataErrors = {};
    templates = [];

    bgcolor = '';
    isLoading = false;

    constructor (
        private activeRoute: ActivatedRoute,
        public api: ApiAdminService,
        private woFlash: WoFlashService,
        private metaPage: MetaPageService,
    ) {
        this.metaPage.setTitle('Scoreboard');
    }

    ngOnInit () {
        this.activeRoute.parent.params.subscribe(parentParams => {
            this.eventId = parseInt(parentParams['id'], 10) || 0;
            this.loadData();
            this.woFlash.show();
        });
    }

    loadData() {
        this.isLoading = true;
        this.api.send('events/scoreboard', {id: this.eventId}).then(res => {
            this.isLoading = false;
            this.scoreboard = res['scoreboard'];
            this.templates = res['templates'];
        });
    }

    getDashboardUrl() {
        let url = '';
        url = window.location.protocol + '//' + window.location.host + '/scoreboard/' + this.scoreboard['access_key'];
        return url;
    }

    copyToClipboard(text) {
        const textarea = document.createElement('textarea');
        textarea.textContent = text;
        textarea.style.position = 'fixed';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
    }

    doSave() {
        this.woFlash.hide();
        this.api.send('events/scoreboard', {id: this.eventId}, this.scoreboard).then(res => {
            if (res['errors']) {
                setTimeout(() => {
                    this.dataErrors = res['errors'];
                    this.woFlash.addError(res['errors']);
                    this.woFlash.show();
                }, 100);
            } else {
                this.dataErrors = {};
                setTimeout(() => {
                    this.woFlash.addMessage('The operation was successfully done!');
                    this.woFlash.show();
                }, 100);
            }
        });
    }

    onImageChanged(event, scoreboard) {
        if (event.result && event.result.file.id) {
            scoreboard['background_image_file_id'] = event.result.file.id;
        } else if (event.remove === true) {
            scoreboard['background_image_file_id'] = null;
        }
    }
}
