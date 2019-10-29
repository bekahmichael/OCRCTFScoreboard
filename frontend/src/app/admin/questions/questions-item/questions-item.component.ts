import {Component, OnInit} from '@angular/core';
import {Router, ActivatedRoute} from '@angular/router';
import {WoFlashService} from '@app/wo-module/wo-flash/wo-flash.service';
import {WoBreadCrumbsService} from '@app/wo-module/wo-breadcrumbs/wo-breadcrumbs.service';
import {MetaPageService} from '@app/core/meta-page.service';
import {ApiAdminService} from '@app/share/api-admin.service';

@Component({
    templateUrl: 'questions-item.component.html',
    styleUrls: ['questions-item.component.css']
})
export class QuestionsItemComponent implements OnInit {
    STATUS_LABELS = [
        'Active',
        'Blocked',
    ];

    TYPES = [
        'Multiple choice',
        'Checkboxes',
        'Dropdown',
        'Open answer',
    ];
    id = 0;
    itemId: number;
    dataForm = {
        'id': 0,
        'is_library_question': 1,
        'status': 0,
        'show_description': 0,
        'description': null,
        'type': 0,
        'points': 0,
        'level': 1,
        'answers' : []
    };
    dataErrors = {};
    criteria = {
        defSort: '',
        sort: '',
    };

    constructor(
        private metaPage: MetaPageService,
        private router: Router,
        private woFlash: WoFlashService,
        private activeRoute: ActivatedRoute,
        private breadcrumbs: WoBreadCrumbsService,
        public api: ApiAdminService,
    ) {
    }

    ngOnInit() {
        this.activeRoute.params.subscribe(params => {
            this.itemId = Number(params['id']) || 0;
            this.metaPage.setTitle(this.itemId === 0 ? 'New' : 'Edit');
            this.woFlash.show();
            this.loadData();
        });
    }

    loadData() {
        if (this.itemId > 0) {
            this.api.send('questions/item', {id: this.itemId}).then(res => {
                if (res['data'] !== null) {
                    this.dataForm = res['data'];
                }
            });
        }
    }

    setQuestionAnswers(answers, question) {
        question['answers'] = answers;
    }

    setQuestionNeedUpdate(needUpdate, question, answersModified) {
    }

    onImageChanged = (event, question) => {
        if (event.result && event.result.file.id) {
            question['file_id'] = event.result.file.id;
        } else if (event.remove === true) {
            question['file_id'] = null;
        }
    }

    doSave() {
        this.woFlash.hide();
        console.log(this.dataForm);
        this.api.send('questions/item', {id: this.itemId}, this.dataForm).then(res => {
            if (res['errors']) {
                setTimeout(() => {
                    this.dataErrors = res['errors'];
                    this.woFlash.addError(res['errors']);
                    this.woFlash.show();
                }, 100);
            } else {
                if (this.itemId === 0) {
                    this.woFlash.addMessage('The operation was successfully done!');
                    this.router.navigate(['/admin/questions', res['data'].id]);
                    this.woFlash.show();
                } else {
                    setTimeout(() => {
                        this.woFlash.addMessage('The operation was successfully done!');
                        this.woFlash.show();
                    }, 100);
                }
            }
        });
    }
 
}
