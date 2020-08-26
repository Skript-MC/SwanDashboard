@extends('errors.layout')

@section('title', $title)
@section('fa', $fa)
@section('message', $message)
@if($redirect)
    @section('redirect', true)
@endif
