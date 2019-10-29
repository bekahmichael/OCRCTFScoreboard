import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { WoModule } from '../wo-module/wo.module';
import { ScoreboardRoutingModule } from './scoreboard-routing.module';
import { ScoreboardComponent } from './scoreboard.component';
import { DisplayComponent } from './display/display.component';
import { ShareModule } from '@app/share/share.module';
import { ScoreboardNotFoundComponent } from './scoreboard-not-found/scoreboard-not-found.component';


@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        WoModule,
        ScoreboardRoutingModule,
        ShareModule,
    ],
    declarations: [
        ScoreboardComponent,
        DisplayComponent,
        ScoreboardNotFoundComponent,
    ]
})
export class ScoreboardModule {}
