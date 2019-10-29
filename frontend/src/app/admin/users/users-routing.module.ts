import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { AuthAdminGuard } from '../../auth/auth-admin-guard.service';

import { UserListComponent } from './user-list/user-list.component';
import { UserDetailComponent } from './user-detail/user-detail.component';

const routes: Routes = [
  { path: '', canActivate: [AuthAdminGuard], component: UserListComponent, data: {role: 'admin'} },
  { path: 'participants', canActivate: [AuthAdminGuard], component: UserListComponent, data: {role: 'public'} },
  { path: ':id', canActivate: [AuthAdminGuard], component: UserDetailComponent },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class UsersRoutingModule {}
