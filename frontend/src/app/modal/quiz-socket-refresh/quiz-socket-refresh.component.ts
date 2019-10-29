import {AfterViewInit, Component, Input} from '@angular/core';
import {IWoDialogComponent} from '@app/wo-module/wo-dialog/wo-dialog.interface';

@Component({
    selector: 'app-quiz-socket-refresh',
    templateUrl: './quiz-socket-refresh.component.html',
    styleUrls: ['./quiz-socket-refresh.component.css']
})
export class QuizSocketRefreshComponent implements AfterViewInit, IWoDialogComponent {
    @Input() dialog: any;
    constructor() { }

    ngAfterViewInit() {
        this.dialog.options.css = {
            width: '500px',
        };
    }

    close() {
        this.dialog.close(false);
    }
}
