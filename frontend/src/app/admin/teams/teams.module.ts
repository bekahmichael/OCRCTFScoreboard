import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { WoModule } from '@app/wo-module/wo.module';
import { TeamsRoutingModule } from './teams-routing.module';
import { TeamListComponent } from './team-list/team-list.component';
import { TeamTabsComponent } from './team-tabs/team-tabs.component';
import { TeamProfileComponent } from './team-profile/team-profile.component';
import { TeamParticipantsComponent } from './team-participants/team-participants.component';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        WoModule,
        TeamsRoutingModule,
    ],
    declarations: [
        TeamListComponent,
        TeamTabsComponent,
        TeamProfileComponent,
        TeamParticipantsComponent,
    ]
})
export class TeamsModule { }
