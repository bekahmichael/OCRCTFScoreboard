import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { WoModule } from '@app/wo-module/wo.module';
import {ShareModule} from '@app/share/share.module';
import { QuestionsRoutingModule } from './questions-routing.module';
import { QuestionsListComponent } from './questions-list/questions-list.component';
import { QuestionsItemComponent } from './questions-item/questions-item.component';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        WoModule,
        QuestionsRoutingModule,
        ShareModule,
    ],
    declarations: [
        QuestionsListComponent,
        QuestionsItemComponent,
    ]
})
export class QuestionsModule { }
