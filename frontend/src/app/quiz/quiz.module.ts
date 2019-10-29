import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { WoModule } from '../wo-module/wo.module';
import { QuizRoutingModule } from './quiz-routing.module';
import { QuizComponent } from './quiz.component';
import {ShareModule} from '@app/admin/share/share.module';
import { QuestionComponent } from './question/question.component';
import {ScrollToModule} from 'ng2-scroll-to-el';
import { LoginComponent } from './login/login.component';


@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        WoModule,
        QuizRoutingModule,
        ShareModule,
        ScrollToModule
    ],
    declarations: [
        QuizComponent,
        QuestionComponent,
        LoginComponent,
    ]
})
export class QuizModule {}
