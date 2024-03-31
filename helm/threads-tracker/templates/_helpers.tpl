{{/*
Expand the name of the chart.
*/}}
{{- define "threads-tracker.name" -}}
{{- default .Chart.Name .Values.nameOverride | trunc 63 | trimSuffix "-" }}
{{- end }}

{{/*
Create a default fully qualified app name.
We truncate at 63 chars because some Kubernetes name fields are limited to this (by the DNS naming spec).
If release name contains chart name it will be used as a full name.
*/}}
{{- define "threads-tracker.fullname" -}}
{{- if .Values.fullnameOverride }}
{{- .Values.fullnameOverride | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- $name := default .Chart.Name .Values.nameOverride }}
{{- if contains $name .Release.Name }}
{{- .Release.Name | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- printf "%s-%s" .Release.Name $name | trunc 63 | trimSuffix "-" }}
{{- end }}
{{- end }}
{{- end }}

{{/*
Create chart name and version as used by the chart label.
*/}}
{{- define "threads-tracker.chart" -}}
{{- printf "%s-%s" .Chart.Name .Chart.Version | replace "+" "_" | trunc 63 | trimSuffix "-" }}
{{- end }}

{{/*
Common labels
*/}}
{{- define "threads-tracker.labels" -}}
helm.sh/chart: {{ include "threads-tracker.chart" . }}
{{ include "threads-tracker.selectorLabels" . }}
{{- if .Chart.AppVersion }}
app.kubernetes.io/version: {{ .Chart.AppVersion | quote }}
{{- end }}
app.kubernetes.io/managed-by: {{ .Release.Service }}
{{- end }}

{{/*
Selector labels
*/}}
{{- define "threads-tracker.selectorLabels" -}}
app.kubernetes.io/name: {{ include "threads-tracker.name" . }}
app.kubernetes.io/instance: {{ .Release.Name }}
{{- end }}


{{/*
 Create the env map for db/redis connection
*/}}
{{- define "threads-tracker.env" -}}
- name: DATABASE_HOST
  value: {{ .Values.config.database.host | required "Missing database host" | quote }}
- name: DATABASE_VERSION
  value: {{ .Values.config.database.postgresVersion | required "Missing database version" | quote }}
- name: DATABASE_PORT
  value: {{ .Values.config.database.port | required "Missing database port" | quote }}
- name: DATABASE_NAME
  value: {{ .Values.config.database.name | required "Missing database name" | quote }}
- name: DATABASE_USER
    {{- if .Values.config.database.userFromSecret.enabled }}
  valueFrom:
    secretKeyRef:
      name: {{ .Values.config.database.userFromSecret.secretName | required "Missing database user secret name" | quote }}
      key: {{ .Values.config.database.userFromSecret.secretKey | required "Missing database user secret key" | quote }}
  {{- else }}
  value: {{ .Values.config.database.user | required "Missing database user"}}
  {{- end }}
- name: DATABASE_PASSWORD
    {{- if .Values.config.database.passwordFromSecret.enabled }}
  valueFrom:
    secretKeyRef:
      name: {{ .Values.config.database.passwordFromSecret.secretName | required "Missing database user secret name" | quote}}
      key: {{ .Values.config.database.passwordFromSecret.secretKey | required "Missing database user secret key" | quote }}
  {{- else }}
  value: {{ .Values.config.database.password | required "Missing database password" | quote }}
  {{- end }}
- name: MESSENGER_TRANSPORT_DSN
  value: "redis://{{ .Values.config.redis.host | required "Missing redis host" }}:{{ .Values.config.redis.port | required "Missing redis port" }}"
- name: LOCK_DSN
  value: "redis://{{ .Values.config.redis.host | required "Missing redis host" }}:{{ .Values.config.redis.port | required "Missing redis port" }}"
- name: MASTODON_SERVER
  value: {{ .Values.config.mastodon.server | required "Missing mastodon server" | quote }}
- name: MASTODON_TOKEN
  {{- if .Values.config.mastodon.tokenFromSecret.enabled }}
    valueFrom:
      secretKeyRef:
        name: {{ .Values.config.mastodon.tokenFromSecret.secretName | required "Missing mastodon token secret name" | quote }}
        key: {{ .Values.config.mastodon.tokenFromSecret.secretKey | required "Missing mastodon token secret key" | quote }}
  {{- else }}
  value: {{ .Values.config.mastodon.token | required "Missing mastodon token" | quote }}
  {{- end }}
{{- if .Values.config.sentry.dsn }}
- name: SENTRY_DSN
  value: {{ .Values.config.sentry.dsn | quote }}
- name: SENTRY_ENVIRONMENT
  value: {{ .Values.config.sentry.env | quote }}
{{- end }}
- name: LOG_LEVEL
  value: {{ .Values.config.logLevel | required "Missing log level" | quote }}
{{- end }}