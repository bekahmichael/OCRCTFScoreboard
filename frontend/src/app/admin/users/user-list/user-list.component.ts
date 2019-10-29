import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { WoBreadCrumbsService } from '@app/wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import { MetaPageService } from '@app/core/meta-page.service';

import { ApiAdminService } from '@app/share/api-admin.service';
import { WoDialogService } from '@app/wo-module/wo-dialog/wo-dialog.service';
import { WoDialogAlertComponent } from '@app/wo-module/wo-dialog/wo-dialog-alert/wo-dialog-alert.component';
import { WoDialogConfirmComponent } from '@app/wo-module/wo-dialog/wo-dialog-confirm/wo-dialog-confirm.component';

import { AdminNewUserComponent } from '@app/modal/admin-new-user/admin-new-user.component';

@Component({
    templateUrl: 'user-list.component.html',
    styleUrls: ['./user-list.component.css'],
})
export class UserListComponent implements OnInit {
    role: string;

    search = {
        page: 0,
        inpCommon: '',
        common: '',
        defSort: 'username',
        sort: 'username',
        filterQuery: ['status', 'username', 'email', 'name', 'cdate_from', 'cdate_to'],
        condition: {status: ''}
    };

    STATUS_LABELS = {
        active:  'Active',
        blocked: 'Blocked',
        deleted: 'Deleted',
    };

    ROLE_LABELS = {
        admin: 'Admin',
        public: 'Public',
    };

    curr_page = 0;
    last_page = 0;
    all_check = false;
    advanced_filters = false;
    loading = true;

    rows = [];

    constructor (
        private metaPage: MetaPageService,
        private router: Router,
        private activeRoute: ActivatedRoute,
        private api: ApiAdminService,
        private breadcrumbs: WoBreadCrumbsService,
        private dialog: WoDialogService,
    ) {
        this.metaPage.setTitle('Users');
        this.breadcrumbs.setLinks([
            {iconClass: 'fa fa-tachometer', route: '/admin', text: 'Home'},
            {text: 'Users'}
        ]);
    }

    ngOnInit () {
        this.activeRoute.data.subscribe((data: any) => {
            this.role = data['role'];

            this.activeRoute.params.subscribe(data => {
                this.search.page = data['p'];
                this.search.sort = this.search.defSort;

                if (data['sort']) {
                    this.search.sort = data['sort'];
                }

                if (data['advanced'] === 'on') {
                    this.advanced_filters = true;
                }

                for (const prop in this.search.filterQuery) {
                    if (typeof data[this.search.filterQuery[prop]] !== 'undefined' && data[this.search.filterQuery[prop]] !== '') {
                        this.search.condition[this.search.filterQuery[prop]] = data[this.search.filterQuery[prop]];
                    }
                }

                if (data['s']) {
                    this.search.inpCommon = data['s'];
                    this.search.common = data['s'];
                } else {
                    this.search.inpCommon = '';
                    this.search.common = '';
                }

                this.findBy();
            });

        });
    }

    toPage(event) {
        this.search.page = event['page'];
        this.toActualUrl();
    }

    findBy() {
        this.loading = true;
        this.search['role'] = this.role;
        this.api.send('user/find', {}, this.search).then(res => {
            this.curr_page = res['curr_page'];
            this.last_page = res['last_page'];
            this.rows      = res['rows'];
            this.all_check = false;
            this.loading   = false;
        });
    }

    checkAllRows(event) {
        for (let i = this.rows.length - 1; i >= 0; i--) {
            this.rows[i].is_checked = event.target.checked;
        }
    }

    sortBy(event) {
        this.search.sort = event.current;
        this.toActualUrl();
    }

    doSearch () {
        this.search.common = this.search.inpCommon;
        this.toActualUrl();
    }

    toActualUrl () {
        const params = {};
        // Page
        if (this.search.page > 1) {
            params['p'] = this.search.page;
        }

        // Sort
        if (this.search.sort !== this.search.defSort) {
            params['sort'] = this.search.sort;
        }

         // Search
        if (this.search.common !== '') {
            params['s'] = this.search.common;
        }

        for (const prop in this.search.filterQuery) {
            if (
                typeof this.search.condition[this.search.filterQuery[prop]] !== 'undefined' &&
                this.search.condition[this.search.filterQuery[prop]] !== ''
            ) {
                params[this.search.filterQuery[prop]] = this.search.condition[this.search.filterQuery[prop]];
            }
        }

         // Advanced filter
        if (this.advanced_filters === true) {
            params['advanced'] = 'on';
        }

        if (this.role === 'public') {
            this.router.navigate(['/admin/users/participants', params]);
        } else {
            this.router.navigate(['/admin/users', params]);
        }
    }

    doChangeStatus (record, status, force = false) {
        let isAllowed = true;
        if (status === 'deleted' && force !== true) {
            isAllowed = false;
            const dialogRef = this.dialog.open(WoDialogConfirmComponent, {message: 'Are you sure you want to delete user?'});
            dialogRef.afterClosed().subscribe(result => {
                if (result) {
                    this.doChangeStatus(record, status, true);
                }
            });
        }

        if (isAllowed) {
            record.status = status;

            this.api.send('user/set-status', {}, {users: [record.id], status: status}).then(data => {
                record.status = status;
                if (
                    status === 'deleted' &&
                    (this.search.condition['status'] === '' || typeof this.search.condition['status'] === 'undefined')
                ) {
                    this.findBy();
                }
            });
        }
    }

    doMassChangeStatus (status, force = false) {
        const items = [];
        for (let i = this.rows.length - 1; i >= 0; i--) {
            if (this.rows[i].is_checked) {
                items.push(this.rows[i].id);
            }
        }

        if (items.length > 0) {
            let isAllowed = true;
            if (status === 'deleted' && force !== true) {
                isAllowed = false;
                const dialogRef = this.dialog.open(WoDialogConfirmComponent, {message: 'Are you sure you want to delete user(s)?'});
                dialogRef.afterClosed().subscribe(result => {
                    if (result) {
                        this.doMassChangeStatus(status, true);
                    }
                });
            }

            if (isAllowed) {
                this.api.send('user/set-status', {}, {users: items, status: status}).then(data => {
                    for (let z = this.rows.length - 1; z >= 0; z--) {
                        if (this.rows[z].is_checked) {
                            this.rows[z].status = status;
                        }
                    }
                    if (
                        status === 'deleted' &&
                        (this.search.condition['status'] === '' || typeof this.search.condition['status'] === 'undefined')
                    ) {
                        this.findBy();
                    }
               });
            }
        } else {
            this.dialog.open(WoDialogAlertComponent, {message: 'You must select user(s).'});
        }
    }

    toggleAdvancedFilter () {
        this.advanced_filters = !this.advanced_filters;
    }

    resetFilter(event: Event) {
        event.preventDefault();

        for (const prop of Object.keys(this.search.condition)) {
            this.search.condition[prop] = '';
        }

        this.search.common = '';
        this.toActualUrl();
    }

    toProfile(id) {
        this.editUser(id);
    }

    editUser(id) {
        const dialogRef = this.dialog.open(AdminNewUserComponent, {id: id, role: this.role});
        dialogRef.afterClosed().subscribe(result => {
            this.findBy();
        });
    }
}
