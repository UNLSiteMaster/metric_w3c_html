# metric_w3c_html

Metric for the w3c html validator

## Install

1. Add the following to the `Config::set('PLUGINS', ` array and customize as needed. This will make SiteMaster aware of the plugin but not apply it to any groups.
```
'metric_w3c_html' => [
  // 'weight' => 20, // Adjust weight if desired
  // 'service_url' => 'https://validator.unl.edu/', // adjust the service URL if desired
],
```

2. Add the following your group configuration under the `METRICS` key. This will enable it for the specified group. All metric configuration will override the defaults set in the `'PLUGINS'` array.
```
'metric_w3c_html' => [
  // 'weight' => 20, // Adjust weight if desired
  // 'service_url' => 'https://validator.unl.edu/', // adjust the service URL if desired
],
```

