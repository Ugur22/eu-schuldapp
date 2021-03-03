<template>
  <nb-container>
	<header :pageTitle="$root.lang.t('appointment')" :method="goBack" />
    <nb-content padder>
      <nb-card v-if="dataIsReady">
        <nb-card-item >
          <nb-body :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center' }">
            <nb-text :style="{ fontSize: 24, color: 'green' }"> {{$root.lang.t('consultant')}}: {{Appointment.consultant.firstname}} {{Appointment.consultant.lastname}}</nb-text>
            <nb-text :style="{ fontSize: 20, color: 'green' }">{{Appointment.title}}</nb-text>
            <nb-card-item>
              <nb-left>
                <nb-text>{{$root.lang.t('time')}}: {{formatDate(Appointment.event_date)}}</nb-text>
              </nb-left>            
              <nb-right>
                <nb-text>{{$root.lang.t('date')}}: {{FormatTime(Appointment.event_date)}}</nb-text>
              </nb-right>
          </nb-card-item>
          <nb-card-item>
              <nb-text :style="{ fontSize: 20, color: '#000' }">{{$root.lang.t('location')}}: {{Appointment.location.name}}</nb-text>
          </nb-card-item>
          <nb-card-item v-if="Appointment.notes">
              <nb-text :style="{ fontSize: 16, color: '#000' }">{{$root.lang.t('note')}}: {{Appointment.notes}}</nb-text>
          </nb-card-item>
          </nb-body>
        </nb-card-item>
      </nb-card>
      <nb-spinner color="#0078ae" v-else /> 
    </nb-content>
    <nb-footer>
      <footer-nav :style="{backgroundColor:'#0078ae'}" activeBtn="appointments"></footer-nav>
    </nb-footer>
  </nb-container>
</template>

<script>
  import FooterNav from '../../included/Footer';
import Header from '../../included/Header';
	import {formatDate,FormatTime} from "../utils/dates";
	import {fetchData} from "../utils/fetch";

  export default {
    props: {
      navigation: {
        type: Object
      }
    },
    components: { FooterNav,Header },
    data() {
      return {
        Appointment:{},
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
				this.Appointment = val[0];
				});
	},
    methods: {
      goBack: function () {
        this.navigation.goBack();
      }
    }
  }
</script>