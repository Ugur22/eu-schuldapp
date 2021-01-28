<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left :style="{flex:1}">
        <nb-button transparent :on-press="goBack" >
          <nb-icon name="arrow-back"/>
        </nb-button>
      </nb-left>
      <nb-body :style="{flex:1}">
      	<nb-title>{{ $root.lang.t('appointment') }} detail</nb-title>
      </nb-body>
      <nb-right :style="{flex:1}">
        <nb-button transparent>
          <nb-icon name="information-circle" />
        </nb-button>
      </nb-right>
    </nb-header>
    <nb-content padder>
      <nb-card v-if="dataIsReady">
        <nb-card-item >
          <nb-body :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center' }">
            <nb-text :style="{ fontSize: 24, color: 'green' }">consulent: {{Appointment.consultant.firstname}} {{Appointment.consultant.lastname}}</nb-text>
            <nb-text :style="{ fontSize: 20, color: 'green' }">{{Appointment.title}}</nb-text>
            <nb-card-item>
              <nb-left>
                <nb-text>tijd: {{formatDate(Appointment.event_date)}}</nb-text>
              </nb-left>            
              <nb-right>
                <nb-text>datum: {{FormatTime(Appointment.event_date)}}</nb-text>
              </nb-right>
          </nb-card-item>
          <nb-card-item>
              <nb-text :style="{ fontSize: 20, color: '#000' }">locatie: {{Appointment.location.name}}</nb-text>
          </nb-card-item>
          <nb-card-item v-if="Appointment.notes">
              <nb-text :style="{ fontSize: 16, color: '#000' }">opmerkingen: {{Appointment.notes}}</nb-text>
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
	import {formatDate,FormatTime} from "../utils/dates";
	import {fetchData} from "../utils/fetch";

  export default {
    props: {
      navigation: {
        type: Object
      }
    },
    components: { FooterNav },
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
<style scoped>

</style>