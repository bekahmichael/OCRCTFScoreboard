import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { WoModule } from '@app/wo-module/wo.module';
import { ShareModule } from '@app/share/share.module';
import { ScoreboardsRoutingModule } from './scoreboards-routing.module';
import { ScoreboardTemplateListComponent } from './scoreboards-template-list/scoreboards-template-list.component';
import { ScoreboardTemplateProfileComponent } from './scoreboard-template-profile/scoreboard-template-profile.component';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        WoModule,
        ShareModule,
        ScoreboardsRoutingModule,
    ],
    declarations: [
        ScoreboardTemplateListComponent,
        ScoreboardTemplateProfileComponent,
    ]
})
export class ScoreboardsModule { }
