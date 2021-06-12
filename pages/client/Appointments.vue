<template>
  <nb-container>
	<header :pageTitle="$root.lang.t('appointments')" :method="goBack" />
     <nb-content padder >
      <nb-card v-if="dataIsReady">
        <nb-card-item header bordered>
          <nb-text class="text">{{$root.lang.t('appointments')}}</nb-text>
        </nb-card-item>
        <nb-list v-if="appointments">
          <nb-list-item  v-for="appointment in appointments"
            :key="appointment.id"  :style="{ padding: 0}" :on-press="() => openAppointment(appointment.id)">
            <nb-left>
              <nb-body v-if="appointment.event_date">
                <nb-text class="text">{{ formatDate(appointment.event_date)}}</nb-text>
                <nb-text class="text">{{ FormatTime(appointment.event_date)}}</nb-text>
              </nb-body>
            </nb-left>
            <nb-body>
                <nb-text v-if="appointment.consultant" class="text">{{appointment.consultant.firstname}} {{appointment.consultant.lastname}} </nb-text>
                <!-- <nb-text v-if="appointment.location" class="text">{{ appointment.location.name }}</nb-text> -->
            </nb-body>
            <nb-right>
              <nb-icon class="text" name="arrow-forward" />
            </nb-right>
          </nb-list-item>
        </nb-list>
      </nb-card>
      <nb-spinner color="#0078ae" v-else /> 
     </nb-content>
    <nb-footer>
      <footer-nav
        :style="{ backgroundColor: '#0078ae' }"
        activeBtn="appointments"
      ></footer-nav>
    </nb-footer>
  </nb-container>
</template>
<style>
.text {
  color: #0078ae;
}
</style>
<script>
import FooterNav from '../../included/Footer';
import Header from '../../included/Header';
import {formatDate,FormatTime} from "../utils/dates";
import {fetchData} from "../utils/fetch";

export default {
  props: {
    navigation: {
      type: Object,
		}
  },
  data() {
    return {
    addAppointment: false,
	  appointments: {},
    dataIsReady: false,
    formatDate,
    FormatTime
    };
  },
  created() {
	},
	mounted() {
		fetchData(`client/appointments`,this.$root.user.token).then(val => {
		this.dataIsReady = true;
			this.appointments = val;
			});
	},
  components: { FooterNav,Header },
  methods: {
    openAppointment: function (id) {
      this.navigation.navigate('AppointmentClient',{
        id:id
      });
    },
    goBack: function () {
      this.navigation.goBack();
    },
  },
};
</script>
