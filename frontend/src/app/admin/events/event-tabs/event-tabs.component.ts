import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { WoBreadCrumbsService } from '@app/wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import { MetaPageService } from '@app/core/meta-page.service';
import { ApiAdminService } from '@app/share/api-admin.service';

import { WoDialogService } from '@app/wo-module/wo-dialog/wo-dialog.service';
import { WoDialogConfirmComponent } from '@app/wo-module/wo-dialog/wo-dialog-confirm/wo-dialog-confirm.component';

@Component({
    templateUrl: 'event-tabs.component.html',
    styleUrls: ['event-tabs.component.css']
})
export class EventTabsComponent implements OnInit {
    static title = '';
    id = 0;
    data;

    constructor (
        private metaPage: MetaPageService,
        private activeRoute: ActivatedRoute,
        private breadcrumbs: WoBreadCrumbsService,
        private api: ApiAdminService,
        private dialog: WoDialogService,
    ) {
        this.breadcrumbs.setLinks([
            {iconClass: 'fa fa-tachometer', route: '/admin', text: 'Home'},
            {iconClass: 'fa fa-calendar-check-o', route: '/admin/events', text: 'Events List'},
            {text: 'Event Profile'},
        ]);
    }

    ngOnInit() {
        this.activeRoute.params.subscribe(params => {
            this.id = parseInt(params['id'], 10) || 0;
            if (this.id > 0) {
                this.loadData();
            } else {
                EventTabsComponent.title = 'New Event';
            }
        });
    }

    loadData() {
        this.api.send('events/profile', {id: this.id}).then(res => {
            EventTabsComponent.title = res['data']['name'];
            this.data = res['data'];
        });
    }

    get staticTitle() {
        return EventTabsComponent.title;
    }

    doChangeQuizStatus(status, row, reset = false) {
        let message = '';

        if (status === 1) {
            message = 'Are you sure you want to start the "' + row['name'] + '"  quiz?';
        } else if (status === 2) {
            message = 'Are you sure you want to pause the "' + row['name'] + '" quiz?';
        } else if (status === 0) {
            message = 'Are you sure you want to restart (wipe all results and reset timer) the "' + row['name'] + '" quiz?';
        } else if (status === 3) {
            message = 'Are you sure you want to finish the "' + row['name'] + '" quiz?';
        }

        const dialogRef = this.dialog.open(WoDialogConfirmComponent, {message: message});
        dialogRef.afterClosed().subscribe(result => {
            if (result === true) {
                row['event2quiz']['status'] = status;

                if (status === 1 && row['event2quiz']['passed_time'] === 0) {
                    row['event2quiz']['passed_time'] = 1 / 60;
                }

                if (reset === true) {
                    row['event2quiz']['passed_time'] = 0;
                }

                this.api.send('quizzes/event-quiz-status', {
                    event_id: row['event2quiz']['event_id'],
                    quiz_id: row['event2quiz']['quiz_id']
                }, {
                    status: row['event2quiz']['status'],
                    reset: reset,
                }).then(res => {});
            }
        });
    }

    doToggleShowFinalResult(row) {
        const dialogRef = this.dialog.open(
            WoDialogConfirmComponent,
            {
                message:    'Are you sure you want to ' +
                            (row['scoreboard']['show_final_results'] === 1 ? 'hide' : 'show') +
                            ' the result on scoreboard for the "' + row['name'] + '" quiz?'
            }
        );
        dialogRef.afterClosed().subscribe(result => {
            if (result === true) {
                row['scoreboard']['show_final_results'] = (row['scoreboard']['show_final_results'] === 1 ? 0 : 1);

                this.api.send('events/scoreboard-options', {
                    event_id: row['event2quiz']['event_id'],
                    quiz_id: row['event2quiz']['quiz_id'],
                }, {
                    show_final_results: row['scoreboard']['show_final_results'],
                }).then(res => {});
            }
        });
    }
}
