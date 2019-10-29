import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { WoBreadCrumbsService } from '@app/wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import { MetaPageService } from '@app/core/meta-page.service';
import { ApiAdminService } from '@app/share/api-admin.service';

import { WoDialogService } from '@app/wo-module/wo-dialog/wo-dialog.service';
import { WoDialogConfirmComponent } from '@app/wo-module/wo-dialog/wo-dialog-confirm/wo-dialog-confirm.component';

@Component({
    templateUrl: 'team-list.component.html',
    styleUrls: ['team-list.component.css'],
})
export class TeamListComponent implements OnInit {
    instance = this;
    criteria = {
        page: 0,
        defSort: 'name',
        sort: '',
        filterQuery: ['common', 'name', 'created_at_from', 'created_at_to', 'status'],
        preFilter: {},
        filter: {},
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
        this.metaPage.setTitle('Teams List');
        this.breadcrumbs.setLinks([
            {iconClass: 'fa fa-tachometer', route: '/admin', text: 'Home'},
            {text: 'Teams List'},
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
        this.api.send('teams/index', this.criteria, {filter: this.criteria.filter}).then(res => {
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

        this.router.navigate(['/admin/teams', params]);
    }

    sortBy(event) {
        this.criteria.sort = event.current;
        this.toActualUrl();
    }

    doSearch () {
        this.criteria.filter = Object.assign({}, this.criteria.preFilter);
        this.toActualUrl();
    }

    toProfile(id) {
        this.router.navigate(['/admin/teams', id]);
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
        this.criteria.filter = {};
        this.toActualUrl();
    }

    doDeleteTeam(row) {
        const dialogRef = this.dialog.open(WoDialogConfirmComponent, {message: 'Are you sure you want to delete team?'});
        dialogRef.afterClosed().subscribe(result => {
            if (result === true) {
                this.api.send('teams/delete', {id: row['id']}, {}).then(res => {
                    this.findBy();
                });
            }
        });
    }

    doActivateTeam(row) {
        const dialogRef = this.dialog.open(WoDialogConfirmComponent, {message: 'Are you sure you want to activate team?'});
        dialogRef.afterClosed().subscribe(result => {
            if (result === true) {
                this.api.send('teams/activate', {id: row['id']}, {}).then(res => {
                    this.findBy();
                });
            }
        });
    }

    doBlockTeam(row) {
        const dialogRef = this.dialog.open(WoDialogConfirmComponent, {message: 'Are you sure you want to block team?'});
        dialogRef.afterClosed().subscribe(result => {
            if (result === true) {
                this.api.send('teams/block', {id: row['id']}, {}).then(res => {
                    this.findBy();
                });
            }
        });
    }

    // doChangeStatus (record, status, force = false) {
    //     let isAllowed = true;
    //     if (status === 'deleted' && force !== true) {
    //         isAllowed = false;
    //         const dialogRef = this.dialog.open(WoDialogConfirmComponent, {message: 'Are you sure you want to delete team?'});
    //         dialogRef.afterClosed().subscribe(result => {
    //             if (result) {
    //                 this.doChangeStatus(record, status, true);
    //             }
    //         });
    //     }

    //     if (isAllowed) {
    //         record.status = status;

    //         this.api.send('team/block', {}, {users: [record.id], status: status}).then(data => {
    //             record.status = status;
    //             // if (
    //             //     status === 'deleted' &&
    //             //     (this.search.condition['status'] === '' || typeof this.search.condition['status'] === 'undefined')
    //             // ) {
    //                 this.findBy();
    //             // }
    //         });
    //     }
    // }
}
