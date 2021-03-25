<template>
  <nb-container>
	<header :pageTitle="$root.lang.t('your_appointment')" :method="goBack" />
    <nb-content padder>
      <view :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center' }">
        <nb-h1>{{$root.lang.t('confirmation_appointment')}}</nb-h1>
      </view>
      <nb-card>
        <nb-card-item>
          <nb-body :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center' }">
            <nb-icon name="checkmark" :style="{ fontSize: 150, color: 'green' }" />
            <nb-text>{{$root.lang.t('client')}}: {{navigation.getParam('clientName')}}</nb-text>
            <nb-card-item>
              <nb-left>
                <nb-text>{{$root.lang.t('time')}}: {{navigation.getParam('time')}}</nb-text>
              </nb-left>            
              <nb-right>
                <nb-text>{{$root.lang.t('date')}}: {{navigation.getParam('date')}}</nb-text>
              </nb-right>
          </nb-card-item>
          <nb-card-item>
            <nb-text>{{$root.lang.t('note')}}: {{navigation.getParam('notes')}}</nb-text>
          </nb-card-item>
          <image :source="require('../../assets/images/logo.png')" />
          </nb-body>
        </nb-card-item>
      </nb-card>
    <nb-button full info class="btns" :on-press="() => goToPage('AppointmentsConsultant')">
      <nb-icon name="arrow-back" :on-press="goBack" />
      <nb-text class="text-btn">{{$root.lang.t('return_appointments')}}</nb-text>
    </nb-button>
    </nb-content>
    <nb-footer>
      <footer-nav :style="{backgroundColor:'#0078ae'}" activeBtn="appointments"></footer-nav>
    </nb-footer>
  </nb-container>
</template>

<script>
  import FooterNav from '../../included/Footer';
import Header from '../../included/Header';

  export default {
    props: {
      navigation: {
        type: Object
      }
    },
    components: { FooterNav,Header },
    data() {
      return {
      };
    },
    methods: {
      goBack: function () {
        this.navigation.goBack();
    },
      goToPage: function (page) {
      	this.navigation.navigate(page);
    },
    }
  }
</script>
<style>
.btns {
  padding:10px;
  background-color:#0078ae;
  margin:10px;
  align-items: center;
  border-radius: 10px;
  justify-content: center;
}

.text-btn {
  font-weight: bold;
}
</style>