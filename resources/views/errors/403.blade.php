@extends('errors.layout')

@section('title', 'Non autorisé')
@section('fa', 'user-slash')
@section('message', $exception->getMessage() ?: 'On dirait que vous essayez de consulter une ressource dont vous n\'avez pas l\'accès.')
@section('redirect', true)
