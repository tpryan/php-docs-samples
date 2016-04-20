# Cloud Storage & Google App Engine

This sample application demonstrates how to use [Cloud Storage with Google App Engine](https://cloud.google.com/appengine/docs/php/googlestorage/).

## Setup

Before running this sample:

## Prerequisites

- Install [`composer`](https://getcomposer.org)
- Install dependencies by running:

```sh
composer install
```

## Setup

Before you can run or deploy the sample, you will need to do the following:

1. Set `<your-bucket-name>` in `index.php` to the name of your Cloud Storage Bucket.

## Deploy to App Engine

**Prerequisites**

- Install the [Google Cloud SDK](https://developers.google.com/cloud/sdk/).

**Deploy with gcloud**

```
gcloud config set project YOUR_PROJECT_ID
gcloud preview app deploy
gcloud preview app browse
```

The last command will open `https://{YOUR_PROJECT_ID}.appspot.com/`
in your browser.
