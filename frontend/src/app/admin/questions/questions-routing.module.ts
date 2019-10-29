import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AuthAdminGuard } from '@app/auth/auth-admin-guard.service';
import { QuestionsListComponent } from './questions-list/questions-list.component';
import { QuestionsItemComponent } from './questions-item/questions-item.component';

const routes: Routes = [
    { path: '', canActivate: [AuthAdminGuard], component: QuestionsListComponent },
    { path: ':id', canActivate: [AuthAdminGuard], component: QuestionsItemComponent},
];

@NgModule({
    imports: [RouterModule.forChild(routes)],
    exports: [RouterModule]
})
export class QuestionsRoutingModule {}
