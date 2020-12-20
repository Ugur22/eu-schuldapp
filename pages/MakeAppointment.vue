<template>
  <nb-container>
   <nb-header :style="{backgroundColor:'#0078ae'}">
      <nb-left>
        <nb-button transparent >
          <nb-icon name="arrow-back" :on-press="goBack" />
        </nb-button>
      </nb-left>
      <nb-body>
        <nb-title>Afspraak maken</nb-title>
      </nb-body>
      <nb-right />
    </nb-header>
    <nb-content padder>
      <nb-card>
        <nb-card-item header>
          <nb-text>Afspraak maken met {{ consultant }}</nb-text>
        </nb-card-item>
        <nb-card-item>
          <nb-body>
            <nb-text :style="{ fontSize: 20 }">{{ days[date.getDay()] }}</nb-text>
            <view :style="{ flexDirection:'row' }">
              <nb-text :style="{ fontSize: 35 }">{{ date.toString().substr(4, 12) }}</nb-text>
              <nb-button rounded info :on-press="showDatepicker" :style="{ marginLeft: 'auto' }">
                <nb-icon active name="calendar" />
              </nb-button>
            </view>
            <view :style="{ flexDirection:'row' }">
              <nb-text :style="{ fontSize: 28 }">{{ ('0'  + (date.getHours())).slice(-2) + ":" + ('0'  + (date.getMinutes())).slice(-2) }}</nb-text>
              <nb-button rounded info :on-press="showTimepicker" :style="{ justifyContent: 'center', alignSelf: 'flex-end' }">
                <nb-icon active name="clock" />
              </nb-button>
            </view>
          </nb-body>            
        </nb-card-item>
        <nb-card-item footer :style="{ flex: 1,  backgroundColor: 'transparent', justifyContent: 'center', alignItems: 'center' }">
          <view :style="{ flexDirection:'row' }">
            <nb-button rounded success :on-press="appointmentMake" :style="{marginRight: 5}">
              <nb-icon active name="check" />
            </nb-button>
            <nb-button rounded danger :on-press="appointmentCancel" :style="{marginLeft: 5}">
              <nb-icon active name="times" />
            </nb-button>
          </view>
        </nb-card-item>
      </nb-card>
      <view>
        <date-time-picker
          v-if="show"
          testID="dateTimePicker"
          :value="date"
          :mode="mode"
          :is24Hour=true
          display="default"
          :minimumDate="minimumDate"
          :minuteInterval="10"
          :onChange="getDateTime"
        />
      </view>
    </nb-content>
    <nb-footer>
      <footer-nav :style="{backgroundColor:'#0078ae'}" activeBtn="appointments"></footer-nav>
    </nb-footer>
  </nb-container>
</template>

<script>
  import FooterNav from '../included/Footer';
  import DateTimePicker from '@react-native-community/datetimepicker';
  import { Platform } from 'react-native';
  
  export default {
    props: {
      navigation: {
        type: Object
      }
    },
    components: { FooterNav, DateTimePicker },
    data() {
      return {
        consultant: 'Erik Jansen',
        date: this.addDays(1),
        mode: 'date',
        show: true,
        minimumDate: this.addDays(1),
        days: ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag']
      };
    },
    methods: {
      goBack: function () {
        this.navigation.goBack();
      },
      getDateTime: function (event, selectedDate) {
        let currentDate = selectedDate || date;
        this.date = currentDate;
        this.show = Platform.OS === 'ios';
        
      },
      showMode: function (currentMode) {
        this.show = true;
        this.mode = currentMode;
      },
      showDatepicker: function () {
        this.showMode('date');
      },
      showTimepicker: function () {
        this.showMode('time');
      },
      addDays: function (days) {
        let date = new Date();
        date.setDate(date.getDate() + days);
        return date;
      },
      appointmentMake: function () {
        alert('making appointment');
      },
      appointmentCancel: function () {
        this.navigation.navigate('Appointments');
      }
    }
  }
</script>