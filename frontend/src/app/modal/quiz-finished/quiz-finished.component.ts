import {AfterViewInit, Component, Input} from '@angular/core';
import {IWoDialogComponent} from '@app/wo-module/wo-dialog/wo-dialog.interface';

@Component({
  selector: 'app-quiz-finished',
  templateUrl: './quiz-finished.component.html',
  styleUrls: ['./quiz-finished.component.css']
})
export class QuizFinishedComponent  implements AfterViewInit, IWoDialogComponent {
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
