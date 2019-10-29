import {AfterViewInit, Component, Input} from '@angular/core';
import {IWoDialogComponent} from '@app/wo-module/wo-dialog/wo-dialog.interface';

@Component({
    selector: 'app-quiz-not-started',
    templateUrl: './quiz-not-started.component.html',
    styleUrls: ['./quiz-not-started.component.css']
})
export class QuizNotStartedComponent  implements AfterViewInit, IWoDialogComponent {
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
