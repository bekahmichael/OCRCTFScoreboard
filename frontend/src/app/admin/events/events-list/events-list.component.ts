import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { WoBreadCrumbsService } from '@app/wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import { MetaPageService } from '@app/core/meta-page.service';
import { ApiAdminService } from '@app/share/api-admin.service';

import { WoDialogService } from '@app/wo-module/wo-dialog/wo-dialog.service';
import { WoDialogConfirmComponent } from '@app/wo-module/wo-dialog/wo-dialog-confirm/wo-dialog-confirm.component';

@Component({
    templateUrl: 'events-list.component.html',
    styleUrls: ['events-list.component.css'],
})
export class EventsListComponent implements OnInit {
    instance = this;
    criteria = {
        page: 0,
        defSort: 'event_date',
        sort: '',
        filterQuery: ['common', 'name', 'event_date_from', 'event_date_to', 'status'],
        preFilter: {},
        filter: {status: ''},
    };

    STATUS_LABELS = {
        0: 'Active',
        1: 'Deleted',
    };

    curr_page = 0;
    last_page = 0;
    advanced_filters = false;
    loading = true;

    rows = [];

    constructor (
        private router: Router,
        private activeRoute: ActivatedRoute,
        private breadcrumbs: WoBreadCrumbsService,
        private metaPage: MetaPageService,
        private api: ApiAdminService,
        private dialog: WoDialogService,
    ) {
        this.metaPage.setTitle('Events List');
        this.breadcrumbs.setLinks([
            {iconClass: 'fa fa-tachometer', route: '/admin', text: 'Home'},
            {iconClass: 'fa fa-tachometer', text: 'Events List'},
        ]);
    }

    ngOnInit () {
        this.activeRoute.params.subscribe(params => {
            this.criteria['page'] = params['p'];
            this.criteria['sort'] = this.criteria.defSort;

            if (params['sort']) {
                this.criteria.sort = params['sort'];
            }

            if (params['advanced'] === 'on') {
                this.advanced_filters = true;
            }

            for (const prop in this.criteria.filterQuery) {
                if (typeof params[this.criteria.filterQuery[prop]] !== 'undefined' && params[this.criteria.filterQuery[prop]] !== '') {
                    this.criteria.filter[this.criteria.filterQuery[prop]] = params[this.criteria.filterQuery[prop]];
                }
            }

            this.criteria.preFilter = Object.assign({}, this.criteria.filter);
            this.findBy();
        });
    }

    findBy() {
        this.loading = true;
        this.api.send('events/index', this.criteria, {filter: this.criteria.filter}).then(res => {
            this.curr_page = res['curr_page'];
            this.last_page = res['last_page'];
            this.rows = res['rows'];
            this.loading = false;
        });
    }

    toActualUrl () {
        const params = {};
        // Page
        if (this.criteria['page'] > 1) {
            params['p'] = this.criteria['page'];
        }

        // Sort
        if (this.criteria.sort !== this.criteria.defSort) {
            params['sort'] = this.criteria['sort'];
        }

        // filter
        for (const prop in this.criteria.filterQuery) {
            if (
                typeof this.criteria.filter[this.criteria.filterQuery[prop]] !== 'undefined' &&
                this.criteria.filter[this.criteria.filterQuery[prop]] !== ''
            ) {
                params[this.criteria.filterQuery[prop]] = this.criteria.filter[this.criteria.filterQuery[prop]];
            }
        }

        // Advanced filter
        if (this.advanced_filters === true) {
            params['advanced'] = 'on';
        }

        this.router.navigate(['/admin/events', params]);
    }

    sortBy(event) {
        this.criteria.sort = event.current;
        this.toActualUrl();
    }

    doSearch () {
        this.criteria.filter = Object.assign({status: ''}, this.criteria.preFilter);
        this.toActualUrl();
    }

    toProfile(id) {
        this.router.navigate(['/admin/events', id]);
    }

    toPage(event) {
        this.criteria.page = event['page'];
        this.toActualUrl();
    }

    toggleAdvancedFilter () {
        this.advanced_filters = !this.advanced_filters;
    }

    resetFilter(event: Event) {
        event.preventDefault();
        this.criteria.filter = {status: ''};
        this.toActualUrl();
    }

    doDelete(row) {
        const dialogRef = this.dialog.open(WoDialogConfirmComponent, {message: 'Are you sure you want to delete event?'});
        dialogRef.afterClosed().subscribe(result => {
            if (result === true) {
                this.api.send('events/delete', {id: row['id']}, {}).then(res => {
                    this.findBy();
                });
            }
        });
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
                }).then(res => {
                });
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
