<template>
  <nb-container>
    <nb-content padder>
      <nb-card>
        <nb-card-item>
          <nb-picker
            mode="dialog"
            placeholder="Type document"
            :selectedValue="selectedDoc"
            :onValueChange="onDocChange"
          >
            <item label="Type document" value="0" />
            <item label="Formulieren" value="form" />
            <item label="Overige documenten" value="others" />
            <item label="Schulden" value="debt" />
          </nb-picker>
        </nb-card-item>
        <nb-card-item>
          <nb-body :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center' }">
            <nb-button rounded info :style="{ alignSelf: 'center',backgroundColor:'#0078ae' }" :on-press="getCamera">
              <nb-icon active name="ios-add" />
            </nb-button>
            <nb-text>{{ $root.lang.t('upload') }}</nb-text>
          </nb-body> 
        </nb-card-item>
        <nb-card-item v-if="displayThumbnail">
            <nb-button transparent :on-press="largerImage" :style="{ flex:1, justifyContent: 'center', alignSelf: 'center' }">
              <nb-thumbnail :source="photo" />
            </nb-button>
        </nb-card-item>
        <nb-card-item :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center' }">
          <nb-badge :style="{ height:'auto' }">
            <nb-text :style="{ fontSize: 14, padding:5 }">{{ $root.lang.t('upload_message') }}</nb-text>
          </nb-badge>
        </nb-card-item>
        <nb-card-item>
          <nb-item regular>
            <nb-input placeholder="Naam schuldeiser" />
          </nb-item>
        </nb-card-item>
        <nb-button class="btns" block info :on-press="sendFile">
          <nb-text>{{ $root.lang.t('send') }}</nb-text>
        </nb-button>
      </nb-card>
    </nb-content>
  </nb-container>
</template>

<script>
  export default {
    props: {
    },
    methods: {
    }
  }
</script>