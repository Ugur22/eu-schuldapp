<template>
  <nb-container>
    <nb-header :style="{backgroundColor:'#0078ae'}">
      <nb-left>
        <nb-button transparent >
          <nb-icon name="arrow-back" :on-press="goBack" />
        </nb-button>
      </nb-left>
      <nb-body>
        <nb-title>Afspraak</nb-title>
      </nb-body>
      <nb-right />
    </nb-header>
    <nb-content padder>
      <nb-button  rounded info :style="{backgroundColor:'#0078ae', position: 'absolute', zIndex: 5, elevation: 5, right: 5, top: 5 }" :on-press="clickAddAppointment">
        <nb-icon  active name="ios-add" />
      </nb-button>
      <nb-card :style="{ marginTop: 20 }" v-if="addAppointment">
        <nb-card-item header bordered>
          <nb-text>Kies een persoon om afspraak te maken</nb-text>
        </nb-card-item>
        <nb-card-item>
          <nb-body>
            <nb-button transparent :on-press="() => makeAppointment(1)">
              <nb-text>Erik Jansen</nb-text>
            </nb-button>
          </nb-body>
          <nb-right>
            <nb-text>( Rotterdam )</nb-text>
          </nb-right>
        </nb-card-item>
        <nb-card-item>
          <nb-body>
            <nb-button transparent :on-press="() => makeAppointment(2)">
              <nb-text>Nikos van Sleeuwen</nb-text>
            </nb-button>
          </nb-body>
          <nb-right>
            <nb-text>( Rotterdam )</nb-text>
          </nb-right>
        </nb-card-item>
      </nb-card>
      <nb-card :style="{ marginTop: 20 }">
        <nb-card-item header bordered>
          <nb-text>Uw afspraken</nb-text>
        </nb-card-item>
        <nb-card-item>
          <nb-body>
            <nb-button transparent :on-press="() => openAppointment(1)">
              <nb-text>10-02-2021</nb-text>
            </nb-button>
          </nb-body>
          <nb-right>
            <nb-text>( Rotterdam )</nb-text>
          </nb-right>
        </nb-card-item>
        <nb-card-item>
          <nb-body>
            <nb-button transparent :on-press="() => openAppointment(2)">
              <nb-text>22-03-2021</nb-text>
            </nb-button>
          </nb-body>
          <nb-right>
            <nb-text>( Rotterdam )</nb-text>
          </nb-right>
        </nb-card-item>
      </nb-card>
    </nb-content>
    <nb-footer>
      <footer-nav :style="{backgroundColor:'#0078ae'}" activeBtn="appointments"></footer-nav>
    </nb-footer>
  </nb-container>
</template>

<script>
  import FooterNav from '../included/Footer';
  export default {
    props: {
      navigation: {
        type: Object
      }
    },
    data() {
      return {
        addAppointment: false
      };
    },
    components: { FooterNav },
    methods: {
      goBack: function () {
        this.navigation.goBack();
      },
      makeAppointment: function (id) {
        this.navigation.navigate('MakeAppointment');
      },
      openAppointment: function (id) {
        this.navigation.navigate('Appointment');
      },
      clickAddAppointment: function (page) {
        this.addAppointment = !this.addAppointment;
      }
    }
  }
</script>