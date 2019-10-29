import { Component, OnInit, HostListener } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { PollingService } from '@app/share/polling.service';
import { environment } from '../../../environments/environment';
import { trigger, state, style, animate, transition } from '@angular/animations';

@Component({
    templateUrl: 'display.component.html',
    styleUrls: ['ribon.css', 'display.component.css'],
    animations: [
        trigger('changeDivSize', [
          state('initial', style({
            backgroundColor: 'green',
            width: '100px',
            height: '100px',
            transform: 'rotate(0deg)',
          })),
          state('final', style({
            backgroundColor: 'red',
            width: '200px',
            height: '200px',
            transform: 'rotate(360deg)',
          })),
          transition('initial=>final', animate('1500ms')),
          transition('final=>initial', animate('1000ms'))
        ]),
        trigger('EnterLeave', [
            state('flyIn', style({ transform: 'translateX(0)' })),
            transition(':enter', [
              style({ transform: 'translateX(-100%)' }),
              animate('500ms ease-in')
            ]),
            transition(':leave', [
              animate('0.3s ease-out', style({ transform: 'translateX(100%)' }))
            ])
        ]),
    ]
})
export class DisplayComponent implements OnInit {
    accessKey: string;
    polling;
    environment;

    data;

    totalTime = null;
    passedTime = 0;

    cooldownTimer = null;
    cooldownTimerActive = false;
    newLeadAnimation = false;

    currentLeader = null;

    minutes_left = null;

    wasGt10min = false;
    wasGt5min = false;

    showGroup = 1;
    maxGroup = 1;
    groupTimer = 0;

    @HostListener('window:resize', ['$event']) onResize(event) {
        this.showGroup = 1;
        this.data.teams.forEach(team => {
            team._group = 1;
        });
        this.autoFillHeightForTable();
    }

    constructor (
        private router: Router,
        private activeRoute: ActivatedRoute,
        private pollingService: PollingService,
    ) {
        this.environment = environment;
    }

    ngOnInit(): void {
        this.activeRoute.params.subscribe(params => {
            this.accessKey = params['access-key'];
            this.startPooling();
        });
        this.tick();
    }

    startPooling() {
        this.polling = this.pollingService.open(this.environment.API_BASE_URL + '/scoreboard/display', {
            'access_key': this.accessKey,
        });
        this.polling.start();
        this.polling.onMessage().subscribe(res => {
            if (res['code'] === 404) {
                this.router.navigate(['/scoreboard/scoreboard-not-found']);
            } else if (res['code'] === 200) {
                if (res['hasChanges'] === true) {
                    this.groupTimer = 0;
                    this.showGroup = 1;
                    this.data = res['data'];

                    this.data['teams'].forEach(team => {
                        team._group = 1;
                    });

                    setTimeout(() => {
                        this.autoFillHeightForTable();
                    }, 150);
                    this.updateTeamLeader();
                }

                this.totalTime = res['data']['time_left'];

                if (this.data['event2quiz']['status'] !== 1) {
                    this.passedTime = 0;
                }
            }
        });
    }

    tick() {
        setTimeout(() => {
            if (this.maxGroup > 1) {
                this.groupTimer++;
                if (this.groupTimer > 10) {
                    this.showGroup = this.showGroup + 1 > this.maxGroup ? 1 : this.showGroup + 1;
                    this.groupTimer = 0;
                }
            }

            if (this.data && this.data['event2quiz']['status'] === 1) {
                if (this.totalTime < 10 && this.totalTime > 0) {
                    this.cooldownTimerActive = true;
                    setTimeout(() => {
                        this.cooldownTimerActive = false;
                    }, 200);
                }

                if (this.totalTime > 602) {
                    this.wasGt10min = true;
                }
                if (this.totalTime > 302) {
                    this.wasGt5min = true;
                }
                if (this.wasGt10min === true && this.totalTime <= 600) {
                    this.showTimeLeft(10);
                }
                if (this.wasGt5min === true && this.totalTime <= 300) {
                    this.showTimeLeft(5);
                }

                this.totalTime -= 1;
            }
            this.tick();
        }, 1000);
    }

    updateTeamLeader() {
        if (this.data['teams'].length > 0 && this.data['teams'][0]['rank'] !== null) {
            const team = this.data['teams'][0];

            if (this.currentLeader !== null && this.currentLeader['id'] !== team['id']) {
                this.showNewLeaderAnimation();
            }

            this.currentLeader = team;
        }
    }

    showNewLeaderAnimation() {
        this.newLeadAnimation = true;

        setTimeout(() => {
            this.newLeadAnimation = false;
        }, 3000);
    }

    showTimeLeft(minutes) {
        this.minutes_left = minutes;
        setTimeout(() => {
            this.minutes_left = null;
            if (minutes === 10) {
                this.wasGt10min = false;
            }
            if (minutes === 5) {
                this.wasGt5min = false;
            }
        }, 4000);
    }

    autoFillHeightForTable() {
        const scoreboardTable = document.querySelector('.scoreboard__table');
        const teamList = document.querySelector('.team-list');
        const tbodyTeamListItem = document.querySelector('.team-list tr');

        const maxY = scoreboardTable.getBoundingClientRect().top + scoreboardTable.getBoundingClientRect().height;


        let lastRightItem = 0;

        this.data['teams'].forEach((t, i) => {
            if (teamList.getBoundingClientRect().top + tbodyTeamListItem.getBoundingClientRect().height * (i + 1) < maxY) {
                lastRightItem = i;
            }
        });

        if (this.data.teams.length !== lastRightItem) {
            let group = 1;
            this.data['teams'].forEach((team, ti) => {
                if (ti !== 0 && ti % (lastRightItem + 1) === 0) {
                    group++;
                }
                team._group = group;
                this.maxGroup = group;
            });
        }
    }
}
