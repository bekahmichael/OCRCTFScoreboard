import {AfterViewInit, Component, Input} from '@angular/core';
import {IWoDialogComponent} from '@app/wo-module/wo-dialog/wo-dialog.interface';

@Component({
  selector: 'app-quiz-paused',
  templateUrl: './quiz-paused.component.html',
  styleUrls: ['./quiz-paused.component.css']
})
export class QuizPausedComponent  implements AfterViewInit, IWoDialogComponent {
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
