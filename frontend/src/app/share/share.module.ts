import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiAdminService } from './api-admin.service';
import { ApiGeneralService } from './api-general.service';
import { ApiMemberService } from './api-member.service';
import { ApiQuizService } from './api-quiz.service';
import { CoreModule } from '../core/core.module';
import { PollingService } from './polling.service';
import { QuestionAnswersListComponent } from './question-answers-list/question-answers-list.component';
import { WoModule } from '@app/wo-module/wo.module';
import { FormsModule } from '@angular/forms';
import { UploadImageComponent } from '@app/share/upload-image/upload-image.component';
import { ConvertFrom24To12Format } from './convert-from-24-to-12-format.pipe';
import { SecondsToTimePipe } from './seconds-to-time.pipe';

@NgModule({
    declarations: [
        QuestionAnswersListComponent,
        ConvertFrom24To12Format,
        SecondsToTimePipe,
        UploadImageComponent,
    ],
    imports: [
        CommonModule,
        CoreModule,
        WoModule,
        FormsModule,
    ],
    providers: [
        ApiAdminService,
        ApiGeneralService,
        ApiMemberService,
        ApiQuizService,
        PollingService,
    ],
    exports: [
        QuestionAnswersListComponent,
        ConvertFrom24To12Format,
        SecondsToTimePipe,
        UploadImageComponent,
    ],
})
export class ShareModule { }
