<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left :style="{flex:1}">
        <nb-button transparent :on-press="goBack" >
          <nb-icon name="arrow-back"/>
        </nb-button>
      </nb-left>
      <nb-body :style="{flex:1}">
      	<nb-title>{{ $root.lang.t('appointments') }}</nb-title>
      </nb-body>
      <nb-right :style="{flex:1}">
        <nb-button transparent>
          <nb-icon name="information-circle" />
        </nb-button>
      </nb-right>
    </nb-header>
     <nb-content padder >
      <nb-card v-if="dataIsReady">
        <nb-card-item header bordered>
          <nb-text class="text">Alle afspraken</nb-text>
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
                <nb-text v-if="appointment.location" class="text">{{ appointment.location.name }}</nb-text>
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

.header {
	color:#fff;
}

.title {
	color: #0078ae;
	font-size:20;
}
</style>
<script>
import FooterNav from '../../included/Footer';
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
  components: { FooterNav },
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
