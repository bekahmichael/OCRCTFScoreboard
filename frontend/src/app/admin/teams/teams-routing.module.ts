import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AuthAdminGuard } from '@app/auth/auth-admin-guard.service';
import { TeamListComponent } from './team-list/team-list.component';
import { TeamTabsComponent } from './team-tabs/team-tabs.component';
import { TeamProfileComponent } from './team-profile/team-profile.component';
import { TeamParticipantsComponent } from './team-participants/team-participants.component';

const routes: Routes = [
    { path: '', canActivate: [AuthAdminGuard], component: TeamListComponent },
    { path: ':id', canActivate: [AuthAdminGuard], component: TeamTabsComponent, children: [
        { path: '', canActivate: [AuthAdminGuard], component: TeamProfileComponent },
        { path: 'participants', canActivate: [AuthAdminGuard], component: TeamParticipantsComponent },
    ] },
];

@NgModule({
    imports: [RouterModule.forChild(routes)],
    exports: [RouterModule]
})
export class TeamsRoutingModule {}
