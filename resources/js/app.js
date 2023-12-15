import './bootstrap.js';
import 'vuestic-ui/css';

import { createApp } from 'vue';
import { createVuestic } from 'vuestic-ui';
import aboutusModule from './pages/aboutus.vue';
import accountModule from './pages/account.vue';
import contactusModule from './pages/contactus.vue';
import dashboardModule from './pages/dashboard.vue';
import enrolledStudentsModule from './pages/enrolledstudents.vue';
import faqModule from './pages/faq.vue';
import loginModule from './pages/login.vue';
import newsModule from './pages/newsevent.vue';
import studentEnrollmentModule from './pages/student/enrollment.vue';
import studentEnrollmentSummaryModule from './pages/student/enrollmentsummary.vue';
import termsconditionModule from './pages/terms.vue';


const app = createApp({
    data () {
        return {
            auth: window.auth,
            path: window.location.pathname,
            config: {
                notifLimit: 5,
                tblCurrPage: 1,
                tblPerPage: 10,
                requirementsPerService: 5,
                uploadSizeLimitInMBytes: 2,
                minPasswordChars: 8,
                strictPasswordUpdate: true,
                contactEmail: 'drivingschool@wheelscapstone.site',
                contactNumberOne: '09493092321',
                contactNumberTwo: '09123456789',
                contactCountryCode: '+63',
                calendar: {
                    openTime: '08:00',
                    closeTime: '17:00',
                    days: [ 1, 2, 3, 4, 5, 6 ],
                },
            },
        };
    },
    components: {
        'login': loginModule,
        'dashboard': dashboardModule,
        'account': accountModule,
        'enrolled-students': enrolledStudentsModule,
        'newsevent': newsModule,
        'student-enrollment': studentEnrollmentModule,
        'student-enrollment-form': studentEnrollmentSummaryModule,
        'faq': faqModule,
        'aboutus': aboutusModule,
        'contactus': contactusModule,
        'terms': termsconditionModule
    },
    methods: {
        arrayFind(array, condition) {
            const item = array.find(condition);
            return array.indexOf(item);
        },
        prompt(msg = "Something went wrong") {
            this.$vaToast.init({
                color: 'primary',
                position: 'bottom-left',
                message: msg,
            });
        },
        redirectToPage(url) {
            if (this.path !== url || this.path === '/dashboard') window.location = url;
        },
        isActivePage(path) {
            return this.path === path;
        },
        tblPagination(array) {
            return this.config.tblPerPage && this.config.tblPerPage !== 0
            ? Math.ceil(array.length / this.config.tblPerPage)
            : array.length;
        },
        fileSizeConverterToBytes(mb) {
            return mb * 1024 * 1024;
        },
        forgeImageFile(data=null,fmod=null,modal=true) {
            let name;
            const path = '/images/';
            if (modal) {
                if (data.length !== 0) {
                    return new Promise((resolve, reject) => {
                        let imgd;
                        name = data[0];
                        const time = new Date().getTime();
                        if (fmod) {
                            const url = path + "uploads/" + fmod + "/" + name;
                            fetch(url)
                            .then(response => {
                                if (!response.ok) throw new Error('Image not found');
                                return response.blob();
                            })
                            .then(blob => {
                                imgd = new File([blob], name, { type: 'image/jpeg', lastModified: time });
                                resolve(imgd);
                            })
                            .catch(error => {
                                imgd = new File([], error.message, { lastModified: time });
                                reject(imgd);
                            })
                        }
                    });
                }
            } else {
                name = data;
                if (name && fmod !== null) return path + "uploads/" + fmod + "/" + name;
                else return path + "default.svg";
            }
        },
    },
});

app.use(createVuestic()).mount('#app');
